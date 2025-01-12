<?php

namespace App\Effects\OngoingEffects;

use App\Abilities\Ability;
use App\Models\Card;
use App\Services\GameStateService;

interface ModifiesAfterAbilityEffect extends OngoingEffect
{
    public function modifyAfterAbilityUse(GameStateService $state, Card $card, Ability $ability): void;
}
