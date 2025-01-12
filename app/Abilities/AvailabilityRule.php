<?php

namespace App\Abilities;

use App\Models\Card;
use App\Services\GameStateService;

interface AvailabilityRule {
    public function isAvailable(GameStateService $state, Card $card): bool;
}