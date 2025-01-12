<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTurn extends Model
{
    protected $fillable = [
        'game_id',
        'turn_number',
        'player_id',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function historyEntries(): HasMany
    {
        return $this->hasMany(GameTurnHistory::class, 'turn_id');
    }
}