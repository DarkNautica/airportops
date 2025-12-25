<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pass_along_attachments', function (Blueprint $table) {
            $table->unsignedInteger('section_index')->nullable()->after('pass_along_id');
            $table->unsignedInteger('row_index')->nullable()->after('section_index');

            $table->index(['pass_along_id', 'section_index', 'row_index'], 'paa_locator_idx');
        });
    }

    public function down(): void
    {
        Schema::table('pass_along_attachments', function (Blueprint $table) {
            $table->dropIndex('paa_locator_idx');
            $table->dropColumn(['section_index', 'row_index']);
        });
    }
};
