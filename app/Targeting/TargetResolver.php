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
    public function getValidTargets(Player $player, array $targetTypes, $gameCards): Collection
    {
        // Start the query builder for GameBoardSpaces
        $query = match (true) {
            in_array(TargetType::OPPONENT, $targetTypes) => $player->getOpponent()->board->spaces(),
            in_array(TargetType::YOU, $targetTypes) => $player->board->spaces(),
            default => throw new InvalidArgumentException('Must specify OPPONENT or YOUR_BOARD')
        };

        return $query
            ->when(in_array(TargetType::EMPTY, $targetTypes), fn($q) => $q->whereNotIn('id', $gameCards->keys()->toArray()))
            ->when(!in_array(TargetType::EMPTY, $targetTypes), fn($q) => $q->whereIn('id', $gameCards->keys()->toArray()))
            ->when(in_array(TargetType::UNPROTECTED, $targetTypes), fn($q) => $q->unprotected())
            ->when(in_array(TargetType::DAMAGED, $targetTypes), fn($q) => $q->damaged())
            ->when(in_array(TargetType::PERSON, $targetTypes), fn($q) => $q->withCardType('PERSON'))
            ->when(in_array(TargetType::CAMP, $targetTypes), fn($q) => $q->withCardType('CAMP'))
            ->pluck('id');
            
    }
}