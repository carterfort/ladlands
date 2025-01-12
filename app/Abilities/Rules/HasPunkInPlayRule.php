<?php

namespace App\Abilities\Rules;

use App\Abilities\AvailabilityRule;
use App\Models\Card;
use App\Services\GameStateService;

class HasPunksInPlayRule implements AvailabilityRule
{
    public function isAvailable(GameStateService $state, Card $card): bool
    {
        return $state->getGameCardsQuery()
            ->whereHas('location', function ($query) use ($card) {
                $query->where('type', 'BATTLEFIELD')
                    ->whereIn('space_id', $card->getOwner()->board->spaces()->pluck('id'));
            })
            ->where('is_punk', true)
            ->exists();
    }
}