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
        Schema::create('cms_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version', 30);                         // e.g. 1.3.0
            $table->string('previous_version', 30)->nullable();    // what it upgraded from
            $table->string('package_name')->nullable();            // filename of uploaded zip
            $table->text('changelog')->nullable();                 // what's new
            $table->enum('status', ['pending','applying','success','failed','rolled_back'])->default('pending');
            $table->string('applied_by')->nullable();              // admin user name
            $table->text('error_message')->nullable();             // if status=failed
            $table->string('backup_path')->nullable();             // pre-update backup location
            $table->json('manifest')->nullable();                  // full manifest.json contents
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_updates');
    }
};
