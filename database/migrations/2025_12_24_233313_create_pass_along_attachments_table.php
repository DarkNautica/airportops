<?php

// database/migrations/xxxx_xx_xx_create_pass_along_attachments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pass_along_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pass_along_id')->constrained('pass_alongs')->cascadeOnDelete();

            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['pass_along_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('pass_along_attachments');
    }
};