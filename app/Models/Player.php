<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    /** @use HasFactory<\Database\Factories\PlayerFactory> */
    use HasFactory;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo{
        return $this->belongsTo(Game::class);
    }

    public function board(): HasOne {
        return $this->hasOne(GameBoard::class);
    }

    public function getOpponent(): Player {
        return $this->game->players()->whereNot('id', $this->id)->firstOrFail();
    }

    public function hand(): HasMany {
        return $this->hasMany(Card::class, 'location->player_id')->where('location->type', 'player_hand');
    }

}
