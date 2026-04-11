<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;

class VerifyAuditChain extends Command
{
    protected $signature = 'audit:verify-chain
        {--limit=0 : Only verify the last N rows}
        {--fix-nulls : Report rows with NULL hashes (created before hash chain was fixed)}';

    protected $description = 'Verify the audit log hash chain integrity.';

    public function handle(): int
    {
        $key = config('audit.hash_key', env('AUDIT_HASH_KEY'));
        if (!$key) {
            $this->error('AUDIT_HASH_KEY not set. Cannot verify chain.');
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $reportNulls = $this->option('fix-nulls');

        $query = AuditLog::query()->orderBy('id');

        if ($limit > 0) {
            $maxId = (int) AuditLog::max('id');
            $start = max(1, $maxId - $limit + 1);
            $query->where('id', '>=', $start);
        }

        $totalRows = 0;
        $verifiedRows = 0;
        $nullHashRows = 0;
        $brokenRows = [];
        $prevHash = null;
        $genesis = str_repeat('0', 64);

        foreach ($query->cursor() as $log) {
            $totalRows++;

            // Rows with NULL hash were created before the chain was fixed
            if ($log->hash === null || $log->hash === '') {
                $nullHashRows++;
                if ($reportNulls) {
                    $this->warn("Row ID {$log->id}: NULL hash (event={$log->event}, type={$log->auditable_type})");
                }
                // Reset chain — the next hashed row should reference
                // the last valid hash or genesis
                continue;
            }

            $expectedPrev = $prevHash ?: $genesis;

            // Check prev_hash linkage
            if (($log->prev_hash ?? '') !== $expectedPrev) {
                $brokenRows[] = [
                    'id' => $log->id,
                    'reason' => 'prev_hash mismatch',
                    'expected' => substr($expectedPrev, 0, 16) . '...',
                    'actual' => substr($log->prev_hash ?? 'NULL', 0, 16) . '...',
                ];
                // Continue checking rest of chain from this point
                $prevHash = $log->hash;
                continue;
            }

            // Recompute hash from canonical payload
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
            $expected = hash_hmac('sha256', $expectedPrev . '|' . $canon, $key);

            if ($log->hash !== $expected) {
                $brokenRows[] = [
                    'id' => $log->id,
                    'reason' => 'hash mismatch',
                    'expected' => substr($expected, 0, 16) . '...',
                    'actual' => substr($log->hash, 0, 16) . '...',
                ];
            } else {
                $verifiedRows++;
            }

            $prevHash = $log->hash;
        }

        // Report
        $this->newLine();
        $this->info("Audit Chain Verification Report");
        $this->info("===============================");
        $this->info("Total rows scanned:   {$totalRows}");
        $this->info("Verified (hash OK):   {$verifiedRows}");
        $this->info("NULL hash (legacy):   {$nullHashRows}");
        $this->info("Broken links:         " . count($brokenRows));

        if (count($brokenRows) > 0) {
            $this->newLine();
            $this->error("CHAIN INTEGRITY FAILURE — broken links found:");
            $this->table(
                ['Row ID', 'Reason', 'Expected', 'Actual'],
                array_map(fn ($r) => [$r['id'], $r['reason'], $r['expected'], $r['actual']], $brokenRows)
            );
            return self::FAILURE;
        }

        if ($nullHashRows > 0) {
            $this->newLine();
            $this->warn("{$nullHashRows} rows have NULL hashes (created before hash chain was enabled).");
            $this->warn("Run `php artisan audit:backfill-hashes` to retroactively compute hashes for these rows.");
        }

        if ($verifiedRows > 0 && count($brokenRows) === 0) {
            $this->newLine();
            $this->info("CHAIN INTEGRITY OK — all hashed rows verified successfully.");
        }

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
