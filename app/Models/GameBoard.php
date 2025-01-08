<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameBoard extends Model
{
    /** @use HasFactory<\Database\Factories\GameBoardFactory> */
    use HasFactory;

    public function spaces(): HasMany {
        return $this->hasMany(GameBoardSpace::class);
    }
}
