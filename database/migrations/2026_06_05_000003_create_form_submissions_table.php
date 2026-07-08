<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('form_id')->constrained('forms')->cascadeOnDelete();
            $table->json('data');               // {field_key: value}
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['form_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
