<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('slider_id')->constrained('sliders')->cascadeOnDelete();
            $table->string('image');             // path or URL
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_label')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('slider_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
