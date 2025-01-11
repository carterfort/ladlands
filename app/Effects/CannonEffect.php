<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class CannonEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Cannon Fire",
        public readonly string $description = "Damage target and self",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // Damage the target
        $state->stateChanger->damageCard($targetCard);

        // Damage self
        $state->stateChanger->damageCard($request->sourceCard);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::OPPONENT,
            TargetType::UNPROTECTED,
            TargetType::BATTLEFIELD
        ];
    }
}