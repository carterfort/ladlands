<?php

namespace App\Targeting;

use App\Models\Card;
use App\Models\GameBoardSpace;
use App\Models\Player;
use App\Targeting\TargetType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class TargetResolver
{
    public function getValidGameboardSpaces(Player $player, array $targetTypes, $gameCards): Collection
    {
        // Start the query builder for GameBoardSpaces
        $board = match (true) {
            in_array(TargetType::OPPONENT, $targetTypes) => $player->getOpponent()->board,
            in_array(TargetType::YOU, $targetTypes) => $player->board,
            default => throw new InvalidArgumentException('Must specify OPPONENT or YOUR_BOARD')
        };

        $query = $board->spaces();

        if (in_array(TargetType::DAMAGED, $targetTypes)){            
            $gameCards = $gameCards->filter(function ($item, $key) {
                return $item[0]['is_damaged'] === true;
            });
        }

        if (in_array(TargetType::UNPROTECTED, $targetTypes)){
            $query->unprotected($gameCards->keys()->toArray());
        }

        return $query->get()->pluck('id');
            
    }
}