<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {

            // JSON payloads for FAA inspection data
            $table->json('header')->nullable()->after('conditions');
            $table->json('checklist')->nullable()->after('header');

            // Workflow / compliance controls
            $table->string('status', 50)->default('draft')->change();
            // values: draft | submitted | locked

            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('locked_at')->nullable()->after('submitted_at');
            $table->foreignId('locked_by')
                ->nullable()
                ->after('locked_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn(['header', 'checklist', 'submitted_at', 'locked_at']);
            $table->dropConstrainedForeignId('locked_by');

            $table->string('status', 50)->default('Open')->change();
        });
    }
};
