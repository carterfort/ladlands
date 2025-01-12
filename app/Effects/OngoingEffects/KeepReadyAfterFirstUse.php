<?php

namespace App\Effects\OngoingEffects;

use App\Abilities\Ability;
use App\Models\Card;
use App\Services\GameStateService;

class KeepReadyAfterFirstUse implements ModifiesAfterAbilityEffect
{
    public function modifyAfterAbilityUse(GameStateService $state, Card $card, Ability $ability): void
    {
        if (!$state->cardHasUsedAbilityThisTurn($card, $ability)) {
            $card->is_ready = true;
            $card->save();
        }
    }
}