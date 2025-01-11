<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerInputRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PlayerInputRequestFactory> */
    use HasFactory;

    protected $casts = [
        'valid_targets' => 'array',
        'selected_targets' => 'array'
    ];

    public function game(): BelongsTo {
        return $this->belongsTo(Game::class);
    }

    public function owningPlayer(): BelongsTo {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function sourceCard(): BelongsTo {
        return $this->belongsTo(Card::class);
    }
}
