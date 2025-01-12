<?php

namespace App\Effects;

use App\Models\Player;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class GunnerEffect implements ApplyToPlayerImmediatelyEffect
{
    public function __construct(
            public readonly string $title = "Spray and Pray",
            public readonly string $description = "Unleash a hail of bullets",
        ){}

    public function apply(GameStateService $state, Player $player): void 
    {
        // Get all unprotected enemy cards in play
        $enemyCards = $player->getOpponent()
                    ->board->spaces()->unprotected()->get();

        // Apply injure effect to each valid target
        foreach ($enemyCards as $card) {
            $state->stateChanger->damageCard($card);
        }
    }

    public function getTargetingRequirements(): array
    {
        return [];
    }
}