<?php

namespace App\Effects;

use App\Models\Player;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

class ExterminatorEffect implements InputDependentEffect
{
    public function __construct(
            $title = "Exterminate",
            $description = "Tires, fires, etc",
        ){}

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targets = $request->owningPlayer->getOpponent()->board->spaces()->pluck('id');
        $damagedCards = $state->getGameCardsQuery()->whereIn('location->space_id', $targets)->where('is_damaged', true)->get();
        foreach ($damagedCards as $card){
            if ($card->getDefinition()->type == 'Person'){
                $state->stateChanger->destroyCard($card);
            }
        }
    }

    public function getTargetingRequirements(): array
    {
        return [];
    }
}