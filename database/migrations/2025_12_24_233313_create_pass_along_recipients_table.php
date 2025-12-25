<?php

// database/migrations/xxxx_xx_xx_create_pass_along_recipients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pass_along_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pass_along_id')->constrained('pass_alongs')->cascadeOnDelete();

            // Either internal user or external email
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email');

            // Delivery tracking
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending|sent|failed
            $table->string('message_id')->nullable();
            $table->text('error')->nullable();

            $table->timestamps();

            $table->index(['pass_along_id', 'status']);
            $table->index(['email']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('pass_along_recipients');
    }
};
