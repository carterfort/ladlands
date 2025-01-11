<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

class LootEffect extends Effect {

    public function applyToGameState(GameStateService $state, $card, Player $initiatingPlayer){
        $state->stateChanger->damageCard($card);
        if ($card->type == "Camp"){
            $state->stateChanger->drawCardsForPlayer($initiatingPlayer, 1);
        }
    }
}
