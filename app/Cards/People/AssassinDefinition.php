<?php

namespace App\Cards\People;

use App\Abilities\AssassinAbility;
use App\Effects\Effect;
use App\Effects\RestoreEffect;

class AssassinDefinition extends PersonDefinition
{
    public string $title = 'Assassin';
    public string $description = 'Silent but deadly';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new AssassinAbility()];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
}