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
        Schema::create('cards', function (Blueprint $table) {
            $table->id('id');
            $table->integer('game_id');
            $table->string('card_definition');
            $table->boolean('is_damaged')->default(false);
            $table->boolean('is_ready')->default(true);
            $table->boolean('face_down')->default(false);
            $table->boolean('flipped')->default(false);
            $table->boolean('is_destroyed')->default(false);
            $table->json('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
