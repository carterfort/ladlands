<?php

namespace App\Effects;

use App\Events\CreatesRequestForOpponent;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class RaidersEffect implements InputDependentEffect, CreatesRequestForOpponent
{
    public function __construct(
        public readonly string $title = "The enemy is at the gates",
        public readonly string $description = "Choose one of your camps to damage.",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        $state->stateChanger->damageCard($targetCard);
    }

    // Key difference: targeting requirements for opponent to choose THEIR OWN camp
    public function getTargetingRequirements(): array
    {
        return [
            TargetType::YOU, // Because this will be asked of the opponent
            TargetType::CAMP,
            TargetType::BATTLEFIELD,
            TargetType::UNDESTROYED
        ];
    }
}
