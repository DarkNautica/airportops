<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {

            // Only add if missing (prevents Forge duplicate-column crash)
            if (!Schema::hasColumn('inspections', 'header')) {
                $table->json('header')->nullable()->after('inspector_id');
            }

            if (!Schema::hasColumn('inspections', 'checklist')) {
                // if header exists, place after header; otherwise after inspector_id
                $after = Schema::hasColumn('inspections', 'header') ? 'header' : 'inspector_id';
                $table->json('checklist')->nullable()->after($after);
            }

            if (!Schema::hasColumn('inspections', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('checklist');
            }

            if (!Schema::hasColumn('inspections', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('is_locked');
            }

            if (!Schema::hasColumn('inspections', 'locked_by')) {
                $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete()->after('locked_at');
            }

            if (!Schema::hasColumn('inspections', 'certified_at')) {
                $table->timestamp('certified_at')->nullable()->after('locked_by');
            }

            if (!Schema::hasColumn('inspections', 'certified_by')) {
                $table->foreignId('certified_by')->nullable()->constrained('users')->nullOnDelete()->after('certified_at');
            }

            // Index: avoid duplicate index crash too
            $indexName = 'inspections_is_locked_inspection_date_index';
            if (!Schema::hasIndex('inspections', $indexName)) {
                $table->index(['is_locked', 'inspection_date'], $indexName);
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            // Index
            $indexName = 'inspections_is_locked_inspection_date_index';
            if (Schema::hasIndex('inspections', $indexName)) {
                $table->dropIndex($indexName);
            }

            // Foreign keys + cols (only if exist)
            if (Schema::hasColumn('inspections', 'certified_by')) {
                $table->dropConstrainedForeignId('certified_by');
            }
            if (Schema::hasColumn('inspections', 'certified_at')) {
                $table->dropColumn('certified_at');
            }

            if (Schema::hasColumn('inspections', 'locked_by')) {
                $table->dropConstrainedForeignId('locked_by');
            }
            if (Schema::hasColumn('inspections', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
            if (Schema::hasColumn('inspections', 'is_locked')) {
                $table->dropColumn('is_locked');
            }

            if (Schema::hasColumn('inspections', 'checklist')) {
                $table->dropColumn('checklist');
            }
            if (Schema::hasColumn('inspections', 'header')) {
                $table->dropColumn('header');
            }
        });
    }
};
