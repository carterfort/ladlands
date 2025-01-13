<?php

namespace App\Effects\OngoingEffects;

use App\Models\Card;
use App\Services\GameStateService;

interface ModifiesAbilitiesEffect extends OngoingEffect
{
    public function modifyAbilities(array $abilities, Card $card, GameStateService $state): array;
}