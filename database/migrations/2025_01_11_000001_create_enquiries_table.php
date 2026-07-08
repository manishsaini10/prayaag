<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Consolidates the blueprint's leads / enquiries / contacts into one
        // typed table. `type` distinguishes contact | admission | enquiry.
        Schema::create('enquiries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('type')->default('contact')->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->string('source')->nullable(); // originating page path
            $table->string('status')->default('new')->index(); // new | read | archived
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
