<?php

namespace App\Effects\OngoingEffects;

use App\Models\Card;
use App\Services\GameStateService;

interface ModifiesCardEntryEffect extends OngoingEffect
{
    public function modifyCardEntry(GameStateService $state, Card $enteringCard): void;
}