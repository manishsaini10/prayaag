<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('template_key')->unique();
            $table->string('module'); // careers, enquiry, newsletter, video_testimonials, mess_menu, chatbot, custom
            $table->string('subject');
            $table->longText('body_html');
            $table->json('available_placeholders')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('locale')->default('en');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
