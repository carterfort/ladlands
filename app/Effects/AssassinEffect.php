<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class AssassinEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Fatal Strike",
        public readonly string $description = "Completely eliminate the target",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // From the effect icons: Destroyed people are discarded
        $state->stateChanger->destroyCard($targetCard);
    }
}