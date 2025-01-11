<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

class AddWaterEffect implements ApplyToPlayerImmediatelyEffect
{
    public function __construct(
        public readonly string $title = "Plus Water",
        public readonly string $description = "Player gains water",
    ){}

    public function apply(GameStateService $state, Player $player): void
    {
        $state->stateChanger->addWaterForPlayer($player, 1);
        
    }

    public function getTargetingRequirements(): array
    {
        return [];
    }
}
