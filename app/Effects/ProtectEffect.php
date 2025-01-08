<?php

namespace App\Effects;

use App\Services\GameStateService;

class ProtectEffect extends Effect
{

    public function applyToGameState(GameStateService $state, $card)
    {
        /// Do something?
    }
}
