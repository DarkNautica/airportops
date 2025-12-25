<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();

            $table->string('insp_number')->unique();              // INSP-000001
            $table->date('inspection_date');
            $table->time('inspection_time')->nullable();

            $table->string('inspection_type', 100);               // AM, PM, Wildlife, Lighting, etc.
            $table->string('surface', 255)->nullable();           // Runway/Taxiway/Apron/etc.
            $table->string('conditions', 255)->nullable();        // Wx/field conditions summary
            $table->text('findings')->nullable();                 // Detailed findings

            $table->string('status', 50)->default('Open');        // Open / Completed / Closed

            $table->foreignId('inspector_id')->constrained('users');

            $table->timestamps();

            $table->index(['inspection_date', 'inspection_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
