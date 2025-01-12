<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

class WaterSiloEffect implements ApplyToPlayerImmediatelyEffect
{
    public function __construct(
        public readonly string $title = "Retrieve Water Silo",
        public readonly string $description = "Move Water Silo to your hand",
    ) {}

    public function apply(GameStateService $state, Player $player): void
    {
        $waterSiloSpace = $player->board->spaces()->type("PERMA")->wherePosition(1)->first();
        $waterSiloCard = $state->getGameCardsQuery()->where('location->space_id', $waterSiloSpace);
        $state->stateChanger->putCardInHandForPlayer($waterSiloCard, $player);
    }

    public function getTargetingRequirements(): array
    {
        return []; // No targeting needed as it affects itself
    }
}
