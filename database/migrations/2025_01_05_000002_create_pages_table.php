<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('layout_id')->nullable()->constrained('page_layouts')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('status')->default('draft');
            // Inline SEO for now; migrates to the dedicated SEO module later.
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
