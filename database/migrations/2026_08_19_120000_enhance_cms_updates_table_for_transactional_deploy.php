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
        Schema::table('cms_updates', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_updates', 'deployment_id')) {
                $table->string('deployment_id', 60)->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('cms_updates', 'stage')) {
                $table->string('stage', 50)->default('pending')->after('status');
            }
            if (!Schema::hasColumn('cms_updates', 'previous_commit')) {
                $table->string('previous_commit', 40)->nullable()->after('previous_version');
            }
            if (!Schema::hasColumn('cms_updates', 'new_commit')) {
                $table->string('new_commit', 40)->nullable()->after('version');
            }
            if (!Schema::hasColumn('cms_updates', 'duration')) {
                $table->decimal('duration', 8, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('cms_updates', 'health_check_result')) {
                $table->json('health_check_result')->nullable()->after('error_message');
            }
            if (!Schema::hasColumn('cms_updates', 'rollback_status')) {
                $table->string('rollback_status', 30)->default('none')->after('health_check_result');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_updates', function (Blueprint $table) {
            $table->dropColumn([
                'deployment_id',
                'stage',
                'previous_commit',
                'new_commit',
                'duration',
                'health_check_result',
                'rollback_status',
            ]);
        });
    }
};
