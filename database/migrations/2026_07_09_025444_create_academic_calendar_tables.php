<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop the old basic calendar table if it exists
        Schema::dropIfExists('academic_calendar');

        // Create classes table if not exists (checked because school structure might have classes)
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('class_name');
                $table->timestamps();
            });
        }

        // Create academic_sessions table
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_name'); // e.g. "2026-2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // Create academic_terms table
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('term_name'); // e.g. "Term 1"
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        // Create academic_calendar_entries table
        Schema::create('academic_calendar_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('term_id')->nullable()->constrained('academic_terms')->onDelete('set null');
            $table->string('title');
            $table->string('category'); // mapped to string for better SQLite compatibility; values: exam, holiday, important_date, working_day_note
            $table->string('sub_type')->nullable(); // e.g. "Half-Yearly Exam", "Summer Vacation", "PTM"
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->boolean('is_working_day')->default(true);
            $table->string('color_tag', 20);
            $table->string('attachment')->nullable();
            $table->foreignUlid('created_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('published'); // values: published, draft
            $table->timestamps();

            // Indexes for faster calendar loading & queries
            $table->index(['session_id', 'start_date']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_entries');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_sessions');
        Schema::dropIfExists('classes');
    }
};
