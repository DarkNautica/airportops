<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. Add soft deletes to all domain models
        // -------------------------------------------------------
        $tables = [
            'users',
            'inspections',
            'work_orders',
            'notams',
            'pass_alongs',
            'pass_along_attachments',
            'pass_along_recipients',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->softDeletes();
                });
            }
        }

        // -------------------------------------------------------
        // 2. Fix FK delete strategies — make all user FKs use
        //    nullOnDelete for consistency with SoftDeletes
        // -------------------------------------------------------

        // inspections.inspector_id: was RESTRICT (default), change to nullOnDelete
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeign(['inspector_id']);
            $table->unsignedBigInteger('inspector_id')->nullable()->change();
            $table->foreign('inspector_id')->references('id')->on('users')->nullOnDelete();
        });

        // work_orders.created_by: was RESTRICT, change to nullOnDelete
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // work_orders.requested_by: was RESTRICT, change to nullOnDelete
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->unsignedBigInteger('requested_by')->nullable()->change();
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
        });

        // work_orders.updated_by: was RESTRICT, change to nullOnDelete
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        // notams.created_by: was cascadeOnDelete (dangerous!), change to nullOnDelete
        Schema::table('notams', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // -------------------------------------------------------
        // 3. Add FK constraint on audit_logs.causer_id
        // -------------------------------------------------------
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('causer_id')->references('id')->on('users')->nullOnDelete();
        });

        // -------------------------------------------------------
        // 4. Fix users.role column default from 'Ops Tech' to 'Ops Staff'
        // -------------------------------------------------------
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('Ops Staff')->change();
            });
        }
    }

    public function down(): void
    {
        // Remove audit_logs FK
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['causer_id']);
        });

        // Revert notams.created_by to cascadeOnDelete
        Schema::table('notams', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        // Revert work_orders FKs to RESTRICT
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->foreign('requested_by')->references('id')->on('users');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users');
        });

        // Revert inspections.inspector_id to RESTRICT
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropForeign(['inspector_id']);
            $table->foreign('inspector_id')->references('id')->on('users');
        });

        // Remove soft deletes
        $tables = [
            'users',
            'inspections',
            'work_orders',
            'notams',
            'pass_alongs',
            'pass_along_attachments',
            'pass_along_recipients',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropSoftDeletes();
                });
            }
        }

        // Revert role default
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('Ops Tech')->change();
            });
        }
    }
};
