<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class AtomicGardenEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Atomic Growth",
        public readonly string $description = "Restore and ready target person",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        $state->stateChanger->restoreCard($targetCard);
        $state->stateChanger->readyCard($targetCard);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::YOU,
            TargetType::DAMAGED,
            TargetType::PERSON
        ];
    }
}