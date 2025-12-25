<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: change TEXT -> JSON safely
        Schema::table('inspections', function (Blueprint $table) {
            $table->json('header')->nullable()->change();
            $table->json('checklist')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->longText('header')->nullable()->change();
            $table->longText('checklist')->nullable()->change();
        });
    }
};
