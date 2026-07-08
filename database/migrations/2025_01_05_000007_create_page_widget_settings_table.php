<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Optional normalized key/value settings (per the blueprint schema).
        // The canonical store is page_widgets.settings (json); this table is
        // available for queryable/indexed settings when needed.
        Schema::create('page_widget_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('widget_id')->constrained('page_widgets')->cascadeOnDelete();
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['widget_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_widget_settings');
    }
};
