<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameTurnHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'game_id',
        'game_turn_id',
        'entry_type',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function gameTurn(): BelongsTo
    {
        return $this->belongsTo(GameTurn::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
