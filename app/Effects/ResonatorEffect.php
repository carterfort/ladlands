<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class ResonatorEffect implements InputDependentEffect
{
    public function __construct(
            public readonly string $title = "Rattle and hum",
            public readonly string $description = "Destroy target unprotected, damaged card",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $card = $state->getGameCardsQuery()->where('location->space_id', $request->selected_spaces[0])->firstOrFail();
        $state->stateChanger->destroyCard($card);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::OPPONENT,
            TargetType::DAMAGED,
            TargetType::UNPROTECTED
        ];
    }
}
