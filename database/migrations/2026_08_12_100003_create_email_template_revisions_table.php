<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_template_revisions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('email_template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body_html');
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_template_revisions');
    }
};
