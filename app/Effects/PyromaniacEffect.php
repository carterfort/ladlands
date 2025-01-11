<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class PyromaniacEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Conflagration",
        public readonly string $description = "Burn target camp",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // Damage the targeted camp
        $state->stateChanger->damageCard($targetCard);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::OPPONENT,
            TargetType::UNPROTECTED,
            TargetType::CAMP,
            TargetType::BATTLEFIELD
        ];
    }
}