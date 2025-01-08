<?php

namespace App\Effects;

use App\Services\GameStateService;

class LootEffect extends Effect {

    public function applyToGameState(GameStateService $state, $card){
        $state->stateChanger->damageCard($card);
        if ($card->type == "Camp"){
            
        }
    }
}
