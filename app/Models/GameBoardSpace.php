<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GameBoardSpace extends Model
{
    /** @use HasFactory<\Database\Factories\GameBoardSpaceFactory> */
    use HasFactory;

    protected $casts = [
        'battlefield_position' => 'array'
    ];

    public function scopeUnprotected($query)
    {
        return $query->whereNotExists(function ($q) {
            $q->select('game_board_spaces.id')
                ->from('game_board_spaces as above')
                ->join('cards', function ($join) {
                    $join->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(cards.location, "$.space_id")) = above.id')
                    ->where('cards.is_destroyed', false);
                })
                ->where('above.game_board_id', DB::raw('game_board_spaces.game_board_id'))
                ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(above.battlefield_position, "$[1]")) = JSON_UNQUOTE(JSON_EXTRACT(game_board_spaces.battlefield_position, "$[1]")) - 1')
                ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(above.battlefield_position, "$[0]")) = JSON_UNQUOTE(JSON_EXTRACT(game_board_spaces.battlefield_position, "$[0]"))');
        });
    }
}
