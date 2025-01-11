<?php

namespace App\Effects;

use App\Models\Player;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class LootEffect implements InputDependentEffect {

    public function __construct(
        public readonly string $title = "Huck it, Finn!",
        public readonly string $description = "Once more unto the breach!",
    ) {}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void 
    {

        $card = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        $state->stateChanger->damageCard($card);
        if ($card->getDefinition()->type == "Camp") {
            $state->stateChanger->drawCardsForPlayer($request->owningPlayer, 1);
        }
    }

    public function getTargetingRequirements(): array
    {
        return
        [TargetType::OPPONENT, TargetType::UNPROTECTED, TargetType::BATTLEFIELD];
    }

}
