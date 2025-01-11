<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class BloodBankEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Blood Donation",
        public readonly string $description = "Sacrifice one to gain water",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        $state->stateChanger->destroyCard($targetCard);
        $state->stateChanger->addWaterForPlayer($request->owningPlayer, 1);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::YOU,
            TargetType::PERSON,
            TargetType::BATTLEFIELD
        ];
    }
}