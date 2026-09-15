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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64)->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('event_type', 50)->default('page_view')->index();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('device', 50)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('language', 20)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('page_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
