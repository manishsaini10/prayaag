<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Enquiries table
        if (!Schema::hasColumn('enquiries', 'sentiment')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable()->after('message');
                $table->tinyInteger('urgency_score')->unsigned()->nullable()->after('sentiment');
                $table->timestamp('sentiment_analyzed_at')->nullable()->after('urgency_score');
            });
        }

        // Testimonials table
        if (!Schema::hasColumn('testimonials', 'sentiment')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable()->after('quote');
                $table->timestamp('sentiment_analyzed_at')->nullable()->after('sentiment');
            });
        }
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['sentiment', 'urgency_score', 'sentiment_analyzed_at']);
        });
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['sentiment', 'sentiment_analyzed_at']);
        });
    }
};
?>
