<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('us-privacy-minors.tables.assurances', 'us_privacy_age_assurances'), function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->unsignedTinyInteger('age');
            $table->string('method', 30);
            $table->string('provider')->nullable();
            $table->timestamp('assured_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('us-privacy-minors.tables.guardians', 'us_privacy_guardian_links'), function (Blueprint $table) {
            $table->id();
            $table->morphs('minor');
            $table->morphs('guardian');
            $table->string('relationship')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('us-privacy-minors.tables.opt_ins', 'us_privacy_minor_opt_ins'), function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            // Null when the consumer (13 to 15) opted in; the guardian link when a parent did.
            $table->unsignedBigInteger('guardian_link_id')->nullable();
            $table->timestamp('opted_in_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('us-privacy-minors.tables.opt_ins', 'us_privacy_minor_opt_ins'));
        Schema::dropIfExists(config('us-privacy-minors.tables.guardians', 'us_privacy_guardian_links'));
        Schema::dropIfExists(config('us-privacy-minors.tables.assurances', 'us_privacy_age_assurances'));
    }
};
