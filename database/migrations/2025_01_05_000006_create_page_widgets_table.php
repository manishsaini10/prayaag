<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_widgets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('column_id')->constrained('page_columns')->cascadeOnDelete();
            $table->string('widget_type');
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // canonical settings store
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_widgets');
    }
};
