<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('instagram_accounts')) {
            Schema::create('instagram_accounts', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('facebook_page_id')->unique();
                $table->string('instagram_business_id')->unique();
                $table->string('username')->nullable();
                $table->string('name')->nullable();
                $table->text('profile_picture')->nullable();
                $table->unsignedInteger('followers')->default(0);
                $table->unsignedInteger('media_count')->default(0);
                $table->text('token');
                $table->text('refresh_token')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('last_sync')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('instagram_media')) {
            Schema::create('instagram_media', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('instagram_account_id')->constrained('instagram_accounts')->cascadeOnDelete();
                $table->string('media_id')->unique();
                $table->string('caption', 2200)->nullable();
                $table->string('media_type');
                $table->text('media_url');
                $table->text('thumbnail_url')->nullable();
                $table->text('permalink')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->json('children')->nullable();
                $table->boolean('is_cached')->default(false);
                $table->unsignedInteger('likes')->default(0);
                $table->unsignedInteger('comments')->default(0);
                $table->json('raw')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['instagram_account_id', 'posted_at']);
            });
        }

        if (!Schema::hasTable('instagram_sync_logs')) {
            Schema::create('instagram_sync_logs', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->foreignUlid('account_id')->nullable()->constrained('instagram_accounts')->nullOnDelete();
                $table->string('status');
                $table->text('message')->nullable();
                $table->json('api_response')->nullable();
                $table->decimal('execution_time', 8, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();
                $table->index(['account_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_sync_logs');
        Schema::dropIfExists('instagram_media');
        Schema::dropIfExists('instagram_accounts');
    }
};
