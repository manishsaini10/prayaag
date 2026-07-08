<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('parent_id')->nullable()->constrained('media_folders')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['parent_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};
