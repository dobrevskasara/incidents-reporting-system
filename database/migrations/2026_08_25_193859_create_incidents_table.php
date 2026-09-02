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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            // Incident type (e.g. harassment, fraud, hate speech...)
            $table->string('type');

            // Age group of the person reporting / affected
            $table->string('age_group');

            // Platform where the incident took place (Facebook, TikTok, ...)
            $table->string('platform');

            // Report processing status
            $table->string('status')->default('reported');

            // Short note/description (optional)
            $table->text('description')->nullable();

            // Date/time the incident was reported - the basis for the periodic reports
            $table->dateTime('reported_at');

            $table->timestamps();

            $table->index('type');
            $table->index('age_group');
            $table->index('platform');
            $table->index('status');
            $table->index('reported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
