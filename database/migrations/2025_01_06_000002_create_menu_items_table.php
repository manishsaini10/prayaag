<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignUlid('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('type')->default('url'); // url | page | custom
            $table->string('url')->nullable();
            $table->foreignUlid('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('target')->default('_self');
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // icon, css class, mega-menu config
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
