<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores admin-defined custom widgets (the "Widget Builder"). Each row becomes
 * a live page-builder widget at boot via App\Core\Builder\Widgets\DynamicWidget,
 * so non-developers can create unlimited widgets with no code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('widget_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('custom');
            $table->json('fields')->nullable();      // [{key,label,type,default}]
            $table->longText('template')->nullable(); // HTML with {{ key }} / {{{ key }}}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widget_definitions');
    }
};
