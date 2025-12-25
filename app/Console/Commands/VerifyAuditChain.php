<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;

class VerifyAuditChain extends Command
{
    protected $signature = 'audit:verify-chain {--limit=0}';
    protected $description = 'Verify the audit log hash chain integrity.';

    public function handle(): int
    {
        $key = config('audit.hash_key', env('AUDIT_HASH_KEY'));
        if (!$key) {
            $this->error('AUDIT_HASH_KEY not set.');
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');

        $q = AuditLog::query()->orderBy('id');

        if ($limit > 0) {
            $maxId = (int) AuditLog::max('id');
            $start = max(1, $maxId - $limit + 1);
            $q->where('id', '>=', $start);
        }

        $prev = null;
        $count = 0;
        $genesis = str_repeat('0', 64);

        foreach ($q->cursor() as $log) {
            $prevHash = $prev ?: $genesis;

            if (($log->prev_hash ?? '') !== $prevHash) {
                $this->error("Chain break at ID {$log->id}: prev_hash mismatch.");
                return self::FAILURE;
            }

            // IMPORTANT: use raw DB string (no timezone conversion)
            $createdAtDb = (string) $log->getRawOriginal('created_at');

            $payload = [
                'event'          => (string) $log->event,
                'auditable_type' => (string) $log->auditable_type,
                'auditable_id'   => $log->auditable_id !== null ? (string) $log->auditable_id : null,
                'causer_id'      => $log->causer_id !== null ? (string) $log->causer_id : null,
                'ip'             => $log->ip !== null ? (string) $log->ip : null,
                'user_agent'     => ($log->user_agent ?? '') !== '' ? (string) $log->user_agent : null,
                'properties'     => is_array($log->properties) ? $log->properties : null,
                'created_at'     => $createdAtDb,
            ];

            $canon = $this->canonicalJson($payload);
            $expected = hash_hmac('sha256', $prevHash . '|' . $canon, $key);

            if (($log->hash ?? '') !== $expected) {
                $this->error("Chain break at ID {$log->id}: hash mismatch.");
                return self::FAILURE;
            }

            $prev = $log->hash;
            $count++;
        }

        $this->info("OK: verified {$count} audit log rows.");
        return self::SUCCESS;
    }

    private function canonicalJson(array $payload): string
    {
        $payload = $this->ksortRecursive($payload);

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function ksortRecursive($value)
    {
        if (!is_array($value)) return $value;

        ksort($value);

        foreach ($value as $k => $v) {
            $value[$k] = $this->ksortRecursive($v);
        }

        return $value;
    }
}
