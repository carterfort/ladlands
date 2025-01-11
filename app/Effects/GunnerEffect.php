<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class GunnerEffect implements Effect
{
    public function __construct(
            public readonly string $title = "Spray and Pray",
            public readonly string $description = "Unleash a hail of bullets",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void 
    {
        // Get all unprotected enemy cards in play
        $enemyCards = $request->owningPlayer->getOpponent()
                    ->board->spaces()->unprotected()->get();

        // Apply injure effect to each valid target
        foreach ($enemyCards as $card) {
            $state->stateChanger->damageCard($card);
        }
    }
}