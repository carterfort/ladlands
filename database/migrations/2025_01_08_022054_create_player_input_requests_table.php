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
        Schema::create('player_input_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->json('valid_targets');
            $table->enum('target_type', ['cards', 'spaces', 'options']);
            $table->string('effect_key');
            $table->boolean('completed', false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_input_requests');
    }
};
