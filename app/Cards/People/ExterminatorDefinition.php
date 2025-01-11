<?php

namespace App\Cards\People;

use App\Abilities\ExterminatorAbility;

class ExterminatorDefinition extends PersonDefinition
{
    public string $title = 'Exterminator';
    public string $description = 'Description for Exterminator';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new ExterminatorAbility()];
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new ExterminatorAbility();
    }
}