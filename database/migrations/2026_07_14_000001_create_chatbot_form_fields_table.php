<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_form_fields', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('label');
            $table->string('field_key', 100)->unique();
            $table->string('field_type', 50); // text, email, tel, select, textarea, number
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable(); // for select type
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('chatbot_leads', function (Blueprint $table) {
            $table->json('form_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_leads', function (Blueprint $table) {
            $table->dropColumn('form_data');
        });

        Schema::dropIfExists('chatbot_form_fields');
    }
};
