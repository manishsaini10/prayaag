<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_rows', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('section_id')->constrained('page_sections')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_rows');
    }
};
