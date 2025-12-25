<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->json('header')->nullable()->after('inspector_id');
            $table->json('checklist')->nullable()->after('header');

            $table->boolean('is_locked')->default(false)->after('checklist');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete()->after('locked_at');

            $table->timestamp('certified_at')->nullable()->after('locked_by');
            $table->foreignId('certified_by')->nullable()->constrained('users')->nullOnDelete()->after('certified_at');

            $table->index(['is_locked', 'inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropIndex(['is_locked', 'inspection_date']);

            $table->dropConstrainedForeignId('certified_by');
            $table->dropColumn('certified_at');

            $table->dropConstrainedForeignId('locked_by');
            $table->dropColumn('locked_at');
            $table->dropColumn('is_locked');

            $table->dropColumn('checklist');
            $table->dropColumn('header');
        });
    }
};
