<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAuditHashChain extends Command
{
    protected $signature = 'audit:backfill-chain {--chunk=500}';
    protected $description = 'Backfill prev_hash/hash on existing audit_logs rows in ID order.';

    public function handle(): int
    {
        $key = config('audit.hash_key', env('AUDIT_HASH_KEY'));
        if (!$key) {
            $this->error('AUDIT_HASH_KEY not set.');
            return self::FAILURE;
        }

        $chunk = (int) $this->option('chunk');
        $genesis = str_repeat('0', 64);

        $total = (int) DB::table('audit_logs')->count();
        $this->info("Backfilling {$total} rows...");

        $prevHash = null;
        $lastId = 0;

        while (true) {
            $rows = DB::table('audit_logs')
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit($chunk)
                ->get();

            if ($rows->isEmpty()) break;

            foreach ($rows as $row) {
                $prev = $prevHash ?: $genesis;

                // IMPORTANT: raw DB timestamp string exactly
                $createdAtDb = (string) $row->created_at;

                $props = null;
                if (!is_null($row->properties) && $row->properties !== '') {
                    $decoded = json_decode($row->properties, true);
                    $props = is_array($decoded) ? $decoded : null;
                }

                $payload = [
                    'event'          => (string) $row->event,
                    'auditable_type' => (string) $row->auditable_type,
                    'auditable_id'   => $row->auditable_id !== null ? (string) $row->auditable_id : null,
                    'causer_id'      => $row->causer_id !== null ? (string) $row->causer_id : null,
                    'ip'             => $row->ip !== null ? (string) $row->ip : null,
                    'user_agent'     => ($row->user_agent ?? '') !== '' ? (string) $row->user_agent : null,
                    'properties'     => $props,
                    'created_at'     => $createdAtDb,
                ];

                $canon = $this->canonicalJson($payload);
                $hash  = hash_hmac('sha256', $prev . '|' . $canon, $key);

                DB::table('audit_logs')
                    ->where('id', $row->id)
                    ->update([
                        'prev_hash'    => $prev,
                        'hash'         => $hash,
                        'hash_algo'    => 'hmac_sha256',
                        'hash_version' => 1,
                    ]);

                $prevHash = $hash;
                $lastId = (int) $row->id;
            }

            $this->line("...up to ID {$lastId}");
        }

        $this->info("Backfill complete. Latest hash: " . ($prevHash ?: '—'));
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
