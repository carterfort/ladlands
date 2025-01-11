<?php

namespace App\Cards\People;

use App\Abilities\AssassinAbility;

class AssassinDefinition extends PersonDefinition
{
    public string $title = 'Assassin';
    public string $description = 'Silent but deadly';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new AssassinAbility()];
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new AssassinAbility();
    }
}