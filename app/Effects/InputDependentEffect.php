<?php

namespace App\Effects;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;

interface InputDependentEffect extends Effect
{
    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void;
}