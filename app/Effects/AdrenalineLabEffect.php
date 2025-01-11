<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class AdrenalineLabEffect implements InputDependentEffect
{
    public function __construct(
        public readonly string $title = "Last Gasp",
        public readonly string $description = "Copy ability and self-destruct",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        // Get the damaged person whose ability we're copying
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // Get the ability from the target card
        $ability = $targetCard->getDefinition()->getBaseAbilities()[0];

        // Validate water cost
        if ($request->owningPlayer->water < $ability->cost) {
            throw new \InvalidArgumentException('Not enough water to activate ability');
        }

        // Use the copied ability
        $state->playerActivatesAbilityViaCard($request->owningPlayer, $ability, $targetCard);

        // Destroy the target card after using its ability
        $state->stateChanger->destroyCard($targetCard);
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::YOU,
            TargetType::DAMAGED,
            TargetType::PERSON,
            TargetType::BATTLEFIELD
        ];
    }
}