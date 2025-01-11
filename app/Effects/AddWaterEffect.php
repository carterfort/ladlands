<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

class AddWaterEffect extends Effect
{
    public function __construct()
    {
        parent::__construct(
            $title = "Plus Water",
            $description = "Damage target unprotected card",
        );
    }

    public function applyToGameState(GameStateService $state, Player $player, $amount = 1)
    {
        $state->stateChanger->addWaterForPlayer($player, $amount);
    }
}
