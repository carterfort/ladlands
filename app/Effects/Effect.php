<?php

namespace App\Effects;

use App\Services\GameStateService;

interface Effect {

    public function applyToGameState(GameStateService $state, $target);

}