<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('instagram_sync_logs') && !Schema::hasColumn('instagram_sync_logs', 'deleted_at')) {
            Schema::table('instagram_sync_logs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('instagram_sync_logs', 'deleted_at')) {
            Schema::table('instagram_sync_logs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
