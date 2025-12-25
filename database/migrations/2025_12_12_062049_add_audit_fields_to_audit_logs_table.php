<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // If old columns exist, migrate them into canonical ones
        if (Schema::hasColumn('audit_logs', 'user_id') && Schema::hasColumn('audit_logs', 'causer_id')) {
            DB::table('audit_logs')
                ->whereNull('causer_id')
                ->whereNotNull('user_id')
                ->update(['causer_id' => DB::raw('user_id')]);
        }

        if (Schema::hasColumn('audit_logs', 'meta') && Schema::hasColumn('audit_logs', 'properties')) {
            // Only copy if properties is null (don’t overwrite newer data)
            $rows = DB::table('audit_logs')->select('id','meta','properties')->get();
            foreach ($rows as $r) {
                if ($r->properties === null && $r->meta !== null) {
                    DB::table('audit_logs')->where('id', $r->id)->update([
                        'properties' => $r->meta
                    ]);
                }
            }
        }

        // Now drop duplicates (only if they exist)
        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('audit_logs', 'user_id')) {
                // if you added FK, drop constraint before dropping column
                try { $table->dropConstrainedForeignId('user_id'); }
                catch (\Throwable $e) { $table->dropColumn('user_id'); }
            }
        });
    }

    public function down(): void
    {
        // Usually we don't rollback audit schema cleanup.
    }
};
