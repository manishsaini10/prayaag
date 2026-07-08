<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_columns', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('row_id')->constrained('page_rows')->cascadeOnDelete();
            $table->integer('width')->default(12); // 12-column grid
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_columns');
    }
};
