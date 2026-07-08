<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('file');                  // path or URL to the file
            $table->string('file_type')->nullable(); // e.g. pdf, docx
            $table->unsignedBigInteger('file_size')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
