<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            // Store form header + checklist payloads
            if (!Schema::hasColumn('inspections', 'header')) {
                $table->json('header')->nullable()->after('inspector_id');
            }

            if (!Schema::hasColumn('inspections', 'checklist')) {
                $table->json('checklist')->nullable()->after('header');
            }

            // Locking
            if (!Schema::hasColumn('inspections', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('checklist');
            }

            if (!Schema::hasColumn('inspections', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('is_locked');
            }

            if (!Schema::hasColumn('inspections', 'locked_by')) {
                $table->foreignId('locked_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('locked_at');
            }

            // Certification evidence
            if (!Schema::hasColumn('inspections', 'certified_at')) {
                $table->timestamp('certified_at')->nullable()->after('locked_by');
            }

            if (!Schema::hasColumn('inspections', 'certified_by')) {
                $table->foreignId('certified_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('certified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {

            // Drop FKs first if they exist
            if (Schema::hasColumn('inspections', 'certified_by')) {
                $table->dropConstrainedForeignId('certified_by');
            }

            if (Schema::hasColumn('inspections', 'locked_by')) {
                $table->dropConstrainedForeignId('locked_by');
            }

            // Then drop columns
            $columns = [];
            foreach (['certified_at','locked_at','is_locked','checklist','header'] as $col) {
                if (Schema::hasColumn('inspections', $col)) {
                    $columns[] = $col;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
