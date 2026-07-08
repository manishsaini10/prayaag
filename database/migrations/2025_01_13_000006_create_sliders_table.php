<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('location')->default('homepage'); // where it appears
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
