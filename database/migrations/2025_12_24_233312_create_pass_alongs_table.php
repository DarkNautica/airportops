<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pass_alongs', function (Blueprint $table) {
            $table->id();

            // Core (matches PDF page 1) :contentReference[oaicite:0]{index=0}
            $table->date('date');
            $table->string('specialist_name')->nullable();
            $table->time('shift_start_time')->nullable();
            $table->time('shift_end_time')->nullable();

            // Daily Task Checklist (matches PDF page 1) :contentReference[oaicite:1]{index=1}
            $table->boolean('am_part_139')->default(false);
            $table->boolean('am_perimeter')->default(false);
            $table->boolean('am_terminal')->default(false);
            $table->boolean('pm_part_139')->default(false);
            $table->boolean('pm_terminal')->default(false);
            $table->boolean('ramp_apron_patrol')->default(false);
            $table->boolean('wildlife_patrol')->default(false);

            // Narrative
            $table->longText('significant_activity')->nullable();

            // Pages 2–3 (blank grid “Pass Along”) :contentReference[oaicite:2]{index=2}
            $table->json('sections')->nullable();

            // Status + lock/cert style
            $table->string('status')->default('draft'); // draft|submitted
            $table->boolean('is_locked')->default(false);

            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['date', 'status']); // this must exist ONLY ONCE
        });
    }

    public function down(): void {
        Schema::dropIfExists('pass_alongs');
    }
};
