<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_conversational_forms', function (Blueprint $table) {
            $table->string('id', 26)->primary();
            $table->string('conversation_id', 26)->index();
            $table->string('form_id', 26);
            $table->json('collected_data')->nullable();
            $table->string('current_field_key', 100)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->string('submission_id', 26)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversational_forms');
    }
};
