<?php

namespace App\Effects\OngoingEffects;

interface ModifiesAbilitiesEffect extends OngoingEffect
{
    public function modifyAbilities(array $abilities, Card $card, GameStateService $state): array;
}