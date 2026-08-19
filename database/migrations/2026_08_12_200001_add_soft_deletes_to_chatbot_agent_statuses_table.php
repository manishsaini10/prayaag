<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chatbot_agent_statuses') && !Schema::hasColumn('chatbot_agent_statuses', 'deleted_at')) {
            Schema::table('chatbot_agent_statuses', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chatbot_agent_statuses') && Schema::hasColumn('chatbot_agent_statuses', 'deleted_at')) {
            Schema::table('chatbot_agent_statuses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
