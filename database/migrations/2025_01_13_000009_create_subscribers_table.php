<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('status')->default('subscribed'); // subscribed | unsubscribed
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
