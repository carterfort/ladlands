<?php

namespace App\Effects;

use App\Models\Player;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class ExterminatorEffect implements InputDependentEffect
{
    public function __construct(
            $title = "Exterminate",
            $description = "Tires, fires, etc",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        // Get the player from the input request
    }
}