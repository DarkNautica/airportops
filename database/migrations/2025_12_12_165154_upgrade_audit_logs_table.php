<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add missing columns safely
        Schema::table('audit_logs', function (Blueprint $table) {
            // Polymorphic subject (auditable)
            if (!Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->string('auditable_type')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->unsignedBigInteger('auditable_id')->nullable()->after('auditable_type');
            }

            // Event name: created/updated/certified/unlocked/etc.
            if (!Schema::hasColumn('audit_logs', 'event')) {
                $table->string('event', 80)->after('auditable_id');
            }

            // Who caused it (user id)
            if (!Schema::hasColumn('audit_logs', 'causer_id')) {
                $table->unsignedBigInteger('causer_id')->nullable()->after('event');
            }

            // Snapshot/meta
            if (!Schema::hasColumn('audit_logs', 'properties')) {
                $table->json('properties')->nullable()->after('causer_id');
            }

            if (!Schema::hasColumn('audit_logs', 'ip')) {
                $table->string('ip', 45)->nullable()->after('properties'); // IPv6 compatible
            }

            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip');
            }
        });

        // 2) Ensure morph columns are NOT NULL (only if you want strictness)
        // If you already have rows with nulls, DON'T force NOT NULL yet.
        // We'll leave them nullable for now to avoid breaking existing rows.

        // 3) Add indexes safely (avoid duplicate key name crash)
        $this->addIndexIfMissing(
            table: 'audit_logs',
            indexName: 'audit_logs_auditable_type_auditable_id_index',
            columns: ['auditable_type', 'auditable_id']
        );

        $this->addIndexIfMissing(
            table: 'audit_logs',
            indexName: 'audit_logs_event_index',
            columns: ['event']
        );

        $this->addIndexIfMissing(
            table: 'audit_logs',
            indexName: 'audit_logs_causer_id_index',
            columns: ['causer_id']
        );
    }

    public function down(): void
    {
        // Down migrations in production audit systems are usually avoided,
        // but here’s a safe-ish rollback that removes indexes + columns
        // only if they exist.

        $this->dropIndexIfExists('audit_logs', 'audit_logs_auditable_type_auditable_id_index');
        $this->dropIndexIfExists('audit_logs', 'audit_logs_event_index');
        $this->dropIndexIfExists('audit_logs', 'audit_logs_causer_id_index');

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'user_agent')) $table->dropColumn('user_agent');
            if (Schema::hasColumn('audit_logs', 'ip')) $table->dropColumn('ip');
            if (Schema::hasColumn('audit_logs', 'properties')) $table->dropColumn('properties');
            if (Schema::hasColumn('audit_logs', 'causer_id')) $table->dropColumn('causer_id');
            if (Schema::hasColumn('audit_logs', 'event')) $table->dropColumn('event');
            if (Schema::hasColumn('audit_logs', 'auditable_id')) $table->dropColumn('auditable_id');
            if (Schema::hasColumn('audit_logs', 'auditable_type')) $table->dropColumn('auditable_type');
        });
    }

    private function addIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();

        if (!$exists) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();

        if ($exists) {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        }
    }
};
