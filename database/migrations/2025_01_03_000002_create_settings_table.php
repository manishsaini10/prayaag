<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('group_id')->nullable()->constrained('setting_groups')->nullOnDelete();
            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
            $table->softDeletes();
            $table->unique('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
