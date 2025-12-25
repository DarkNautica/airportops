<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use RuntimeException;

class Audit
{
    public static function log(Model $model, string $event, array $properties = []): void
    {
        $auditableType = $model->getMorphClass();
        $auditableId   = $model->getKey();
        $causerId      = Auth::id();
        $ip            = Request::ip();
        $userAgent     = substr((string) Request::userAgent(), 0, 255);

        // IMPORTANT: deterministic timestamp string (no timezone conversions)
        $createdAtDb = now()->format('Y-m-d H:i:s');

        self::acquireChainLock();

        try {
            DB::transaction(function () use (
                $event,
                $auditableType,
                $auditableId,
                $causerId,
                $properties,
                $ip,
                $userAgent,
                $createdAtDb
            ) {
                $prev = AuditLog::query()->orderByDesc('id')->value('hash');
                $prevHash = $prev ?: str_repeat('0', 64);

                $payload = [
                    'event'          => (string) $event,
                    'auditable_type' => (string) $auditableType,
                    'auditable_id'   => (string) $auditableId,
                    'causer_id'      => $causerId ? (string) $causerId : null,
                    'ip'             => $ip ? (string) $ip : null,
                    'user_agent'     => $userAgent !== '' ? $userAgent : null,
                    'properties'     => $properties ?: null,
                    'created_at'     => $createdAtDb, // <- raw DB-style timestamp
                ];

                $canon = self::canonicalJson($payload);
                $hash  = self::hmacSha256($prevHash . '|' . $canon);

                AuditLog::create([
                    'event'          => $event,
                    'auditable_type' => $auditableType,
                    'auditable_id'   => $auditableId,
                    'causer_id'      => $causerId,
                    'properties'     => $properties ?: null,
                    'ip'             => $ip,
                    'user_agent'     => $userAgent,

                    'prev_hash'      => $prevHash,
                    'hash'           => $hash,
                    'hash_algo'      => 'hmac_sha256',
                    'hash_version'   => 1,

                    // store EXACT string we hashed
                    'created_at'     => $createdAtDb,
                ]);
            }, 1);
        } finally {
            self::releaseChainLock();
        }
    }

    private static function hmacSha256(string $data): string
    {
        $key = config('audit.hash_key', env('AUDIT_HASH_KEY'));

        if (!$key) {
            throw new RuntimeException('AUDIT_HASH_KEY is not set. Hash chain cannot be computed.');
        }

        return hash_hmac('sha256', $data, $key);
    }

    private static function canonicalJson(array $payload): string
    {
        $payload = self::ksortRecursive($payload);

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function ksortRecursive($value)
    {
        if (!is_array($value)) return $value;

        ksort($value);

        foreach ($value as $k => $v) {
            $value[$k] = self::ksortRecursive($v);
        }

        return $value;
    }

    private static function acquireChainLock(): void
    {
        $name = config('audit.lock_name', env('AUDIT_HASH_LOCK', 'airportops_audit_chain'));
        $wait = (int) config('audit.lock_wait', env('AUDIT_HASH_LOCK_WAIT', 10));

        $row = DB::selectOne('SELECT GET_LOCK(?, ?) AS lck', [$name, $wait]);

        if (!($row && (int) $row->lck === 1)) {
            throw new RuntimeException("Could not acquire audit chain lock ({$name}).");
        }
    }

    private static function releaseChainLock(): void
    {
        $name = config('audit.lock_name', env('AUDIT_HASH_LOCK', 'airportops_audit_chain'));
        DB::selectOne('SELECT RELEASE_LOCK(?)', [$name]);
    }
}
