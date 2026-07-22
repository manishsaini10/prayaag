<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mess_menus', function (Blueprint $table) {
            $table->string('id', 26)->primary();
            $table->string('title')->default('Weekly Mess Menu');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('created_by', 26)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mess_menu_items', function (Blueprint $table) {
            $table->string('id', 26)->primary();
            $table->string('mess_menu_id', 26);
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->enum('meal_type', ['breakfast', 'lunch', 'snacks', 'dinner']);
            $table->json('items');
            $table->string('notes', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('mess_menu_id')->references('id')->on('mess_menus')->onDelete('cascade');
            $table->index(['day_of_week', 'meal_type']);
        });

        Schema::create('mess_menu_special_days', function (Blueprint $table) {
            $table->string('id', 26)->primary();
            $table->string('mess_menu_id', 26);
            $table->date('date')->index();
            $table->string('label')->nullable();
            $table->enum('meal_type', ['breakfast', 'lunch', 'snacks', 'dinner']);
            $table->json('items');
            $table->timestamps();

            $table->foreign('mess_menu_id')->references('id')->on('mess_menus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_menu_special_days');
        Schema::dropIfExists('mess_menu_items');
        Schema::dropIfExists('mess_menus');
    }
};
