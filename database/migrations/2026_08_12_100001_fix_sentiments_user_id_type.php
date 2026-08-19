<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sentiments') && Schema::hasColumn('sentiments', 'user_id')) {
            Schema::table('sentiments', function (Blueprint $table) {
                // Change user_id from unsignedBigInteger to string(26) to match User ULID primary key
                $table->string('user_id', 26)->nullable()->change();
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sentiments') && Schema::hasColumn('sentiments', 'user_id')) {
            Schema::table('sentiments', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }
    }
};
