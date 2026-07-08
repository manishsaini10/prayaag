<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('author');
            $table->string('role')->nullable();
            $table->longText('quote');
            $table->string('photo')->nullable(); // path or URL
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
