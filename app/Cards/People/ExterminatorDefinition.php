<?php

namespace App\Cards\People;

use App\Abilities\ExterminatorAbility;
use App\Effects\Effect;
use App\Effects\RestoreEffect;

class ExterminatorDefinition extends PersonDefinition
{
    public string $title = 'Exterminator';
    public string $description = 'Description for Exterminator';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new ExterminatorAbility()];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
}