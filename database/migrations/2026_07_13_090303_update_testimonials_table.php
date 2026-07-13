<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('student_name')->nullable();
            $table->string('class')->nullable();
            $table->string('relation')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('title')->nullable();
            $table->longText('testimonial')->nullable();
            $table->tinyInteger('rating')->default(5);
            $table->string('image')->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('featured')->default(false);
            $table->json('display_location')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('browser')->nullable();
        });

        // Safe copy of existing columns if they exist
        if (Schema::hasColumn('testimonials', 'author')) {
            \Illuminate\Support\Facades\DB::table('testimonials')->chunkById(100, function ($testimonials) {
                foreach ($testimonials as $t) {
                    \Illuminate\Support\Facades\DB::table('testimonials')
                        ->where('id', $t->id)
                        ->update([
                            'name' => $t->author,
                            'testimonial' => $t->quote,
                            'image' => $t->photo,
                            'status' => $t->is_published ? 'approved' : 'pending',
                            'display_location' => json_encode(['home', 'testimonials']),
                        ]);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'name', 'student_name', 'class', 'relation', 'phone', 'email',
                'title', 'testimonial', 'rating', 'image', 'status', 'featured',
                'display_location', 'approved_by', 'approved_at', 'ip_address', 'browser'
            ]);
        });
    }
};
