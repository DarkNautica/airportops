<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->char('prev_hash', 64)->nullable()->after('user_agent');
            $table->char('hash', 64)->nullable()->after('prev_hash');
            $table->string('hash_algo', 20)->default('hmac_sha256')->after('hash');
            $table->unsignedTinyInteger('hash_version')->default(1)->after('hash_algo');

            $table->index('hash');
            $table->index('prev_hash');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['hash']);
            $table->dropIndex(['prev_hash']);

            $table->dropColumn(['prev_hash', 'hash', 'hash_algo', 'hash_version']);
        });
    }
};
