<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notams', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('notam_number')->unique(); // e.g. AVL-000001 or NOTAM-000001
            $table->string('station')->default('KAVL'); // allow other airports later

            // Core NOTAM fields
            $table->string('subject')->nullable(); // "RWY 17/35 EDGE LIGHTS OTS" etc
            $table->enum('category', [
                'Runway',
                'Taxiway',
                'Apron/Ramp',
                'Lighting',
                'NAVAID',
                'Obstruction/Crane',
                'Construction',
                'Other',
            ])->default('Other');

            $table->enum('status', ['Draft', 'Active', 'Expired', 'Cancelled'])->default('Draft');

            // Validity windows
            $table->dateTime('effective_from')->nullable();
            $table->dateTime('effective_to')->nullable();

            // Raw text as filed + optional structured data
            $table->text('notam_text'); // the actual NOTAM text (what you would publish)
            $table->json('meta')->nullable(); // optional: runway, taxiway, lights, etc.

            // Ownership / review
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Optional lock (same philosophy as inspections)
            $table->boolean('is_locked')->default(false);
            $table->dateTime('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['station', 'status']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notams');
    }
};
