<?php

namespace App\Effects;

use App\Services\GameStateService;

class DamageEffect extends Effect {
    public function __construct() {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected card",
        );
    }

    public function applyToGameState(GameStateService $state, $card){
        $state->stateChanger->damageCard($card);
    }
}