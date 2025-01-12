<?php

namespace App\Effects;

use App\Models\GameBoardSpace;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetType;

class GainPunkEffect implements InputDependentEffect
{
    public function __construct(
            $title = "Gain Punk",
            $description = "Place a punk in your gameboard",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $spaces = GameBoardSpace::whereIn('id', $request->selected_targets)->get();
        foreach ($spaces as $space){
            $state->stateChanger->putPunkInSpace($state, $space);
        }
    }

    public function getTargetingRequirements(): array
    {
        return [TargetType::YOU, TargetType::EMPTY];
    }
}