<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

interface ApplyToPlayerImmediatelyEffect extends Effect
{
    public function apply(GameStateService $state, Player $player): void;
}
