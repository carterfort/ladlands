<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class InjureEffect implements InputDependentEffect {
    public function __construct(
        public readonly string $title = "Injure",
        public readonly string $description = "Damage target unprotected person",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $card = $state->getGameCardsQuery()->where('location.space_id', $request->selected_spaces[0])->firstOrFail();
        $state->stateChanger->damageCard($card);
    }

    public function getTargetingRequirements(): array
    {
        return [TargetType::OPPONENT, TargetType::UNPROTECTED, TargetType::BATTLEFIELD, TargetType::PERSON];
    }
}