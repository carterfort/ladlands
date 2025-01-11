<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class RestoreEffect implements InputDependentEffect
{
    public function __construct(
            $title = "Gunner",
            $description = "Description for Gunner effect",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $card = $state->getGameCardsQuery()->where('location->space_id',$request->selected_spaces[0])->first();
        $state->stateChanger->restoreCard($card);
    }

    public function getTargetingRequirements(): array
    {
        return [TargetType::YOU, TargetType::DAMAGED];
    }
}
