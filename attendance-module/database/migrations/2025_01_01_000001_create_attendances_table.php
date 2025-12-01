<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained();
            $table->uuid('lms_user_id');
            $table->enum('mode', ['onsite', 'remote']);
            $table->float('geo_confidence')->default(0);
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('ip_hash')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('challenge_passed_at')->nullable();
            $table->timestamp('last_beacon_at')->nullable();
            $table->json('flags')->nullable();
            $table->timestamps();

            $table->unique(['training_session_id', 'lms_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
