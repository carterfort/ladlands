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
            $table->foreignId('game_board_id')->index();
            $table->integer('position');
            $table->enum('type', ['BATTLEFIELD', 'PERMA', 'EVENT']);
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
