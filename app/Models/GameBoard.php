<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class GameBoard extends Model
{
    /** @use HasFactory<\Database\Factories\GameBoardFactory> */
    use HasFactory;

    public $timestamps = false;

    public function spaces(): HasMany {
        return $this->hasMany(GameBoardSpace::class);
    }

    public function battlefield(): HasMany{
        return $this->spaces()->type('BATTLEFIELD');
    }

    public function unprotectedSpaces($occupiedSpaceIds)
    {
        return $this->spaces()
            ->whereIn('id', $occupiedSpaceIds)
            ->where('position', function ($query) use ($occupiedSpaceIds) {
                $query->select('position')
                ->from('game_board_spaces')
                ->whereIn('id', $occupiedSpaceIds);  // Use the IDs here instead of positions
            });
    }

}
