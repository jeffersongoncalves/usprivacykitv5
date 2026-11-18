<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('us-privacy-opt-out.table', 'us_privacy_opt_outs'), function (Blueprint $table) {
            $table->id();
            // String keys: subjects may use int, UUID or ULID primary keys.
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            // Visitors without an account (cookie or browser signal).
            $table->string('anonymous_id', 64)->nullable();
            $table->string('right', 30);
            $table->string('action', 10);
            $table->string('state', 2)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id', 'right']);
            $table->index(['anonymous_id', 'right']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('us-privacy-opt-out.table', 'us_privacy_opt_outs'));
    }
};
