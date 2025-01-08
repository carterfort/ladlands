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
        Schema::create('game_board_spaces', function (Blueprint $table) {
            $table->id();
            $table->integer('game_board_id');
            $table->json('battlefield_position');
            $table->enum('type', ['BATTLEFIELD', 'PERMA', 'EVENT']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_board_spaces');
    }
};
