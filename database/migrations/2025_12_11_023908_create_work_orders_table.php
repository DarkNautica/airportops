<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('work_orders', function (Blueprint $table) {
        $table->id();

        // Human-readable work order number (e.g. WO-000001)
        $table->string('wo_number')->unique();

        $table->string('title');
        $table->text('description')->nullable();

        $table->string('location')->nullable();

        // For now keep these as strings; we can later constrain with enums if we want
        $table->string('priority', 50)->default('Normal'); // Normal, High, Critical, etc.
        $table->string('status', 50)->default('Open');     // Open, In Progress, Closed, etc.

        $table->date('due_date')->nullable();

        // Who requested and who created/updated it
        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('requested_by')->constrained('users');
        $table->foreignId('updated_by')->nullable()->constrained('users');

        // When it was actually completed
        $table->timestamp('completed_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
