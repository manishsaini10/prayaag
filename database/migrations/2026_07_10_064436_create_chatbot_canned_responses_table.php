<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_canned_responses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('shortcut', 50);
            $table->text('body');
            $table->string('category', 50)->nullable();
            $table->foreignUlid('department_id')->nullable()->constrained('chatbot_departments')->nullOnDelete();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('shortcut');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_canned_responses');
    }
};
