<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class DamageEffect implements InputDependentEffect {
    public function __construct(
        public readonly string $title = "Damage",
        public readonly string $description = "Damage target unprotected card",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $card = $state->getGameCardsQuery()->where('location.space_id', $request->selected_spaces[0])->firstOrFail();
        $state->stateChanger->damageCard($card);
    }
}