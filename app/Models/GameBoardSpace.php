<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameBoardSpace extends Model
{
    /** @use HasFactory<\Database\Factories\GameBoardFactory> */
    use HasFactory;

    public $timestamps = false;

    public function board(): BelongsTo {
        return $this->belongsTo(GameBoard::class);
    }

    public function scopeType(Builder $query, $type){
        $query->whereType($type);
    }

    public function scopeUnprotected(Builder $query, $occupiedSpaceIds){

        $placeholders = implode(',', array_fill(0, count($occupiedSpaceIds), '?'));
        if ($placeholders == ""){
            $query->where('game_board_id', '<', 0);
            return;
        }
        $query->type("BATTLEFIELD")
            ->whereIn('id', $occupiedSpaceIds)
            ->selectRaw('id, position, (position % 3) as column_index')
            ->whereRaw('position = (SELECT MIN(position) 
                     FROM game_board_spaces AS inner_spaces 
                     WHERE inner_spaces.id IN ('.$placeholders.') 
                     AND (inner_spaces.position % 3) = (game_board_spaces.position % 3))', [$occupiedSpaceIds]);
    }
}
