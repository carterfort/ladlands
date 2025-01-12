<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class BanishEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Banish",
        public readonly string $description = "Remove target from existence",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        $state->stateChanger->destroyCard($targetCard);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::OPPONENT,
            TargetType::PERSON,
            TargetType::BATTLEFIELD
        ];
    }
}
