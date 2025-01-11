<?php

namespace App\Effects;

use App\Services\GameStateService;

class DestroyEffect extends Effect
{
    public function __construct()
    {
        parent::__construct(
            $title = "Destroy",
            $description = "Destroy target card",
        );
    }

    public function applyToGameState(GameStateService $state, $card)
    {
        $state->stateChanger->destroyCard($card);
    }
}
