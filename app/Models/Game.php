<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = ['current_player_id'];

    public function currentPlayer(): HasOne {
        return $this->hasOne(Player::class);
    }
    public function players(): HasMany {
        return $this->hasMany(Player::class);
    }

    public function playerA(): HasOne {
        return $this->hasOne(Player::class)->whereGameSeat('A');
    }

    public function playerOne(): HasOne {
        return $this->hasOne(Player::class)->whereGameSeat('One'); 
    }

    public function cards(): HasMany {
        return $this->hasMany(Card::class);
    }
    
    public function playerInputRequests(): HasMany {
        return $this->hasMany(PlayerInputRequest::class);
    }

}
