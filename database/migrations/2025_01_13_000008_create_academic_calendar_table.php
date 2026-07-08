<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Folds in holidays: `type` distinguishes holiday | term | exam | event.
        Schema::create('academic_calendar', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('type')->default('event'); // holiday | term | exam | event
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('starts_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar');
    }
};
