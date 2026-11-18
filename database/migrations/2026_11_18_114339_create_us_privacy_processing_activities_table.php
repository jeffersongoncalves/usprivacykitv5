<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('us-privacy-notices.table', 'us_privacy_processing_activities'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('purpose');
            $table->json('consumers')->nullable();
            $table->json('data_categories')->nullable();
            $table->json('models')->nullable();
            $table->json('sources')->nullable();
            $table->json('recipients')->nullable();
            $table->string('retention')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('us-privacy-notices.table', 'us_privacy_processing_activities'));
    }
};
