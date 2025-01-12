<?php

namespace App\Cards\People;

use App\Abilities\Definitions\AssassinAbility;
use App\Abilities\BaseAbility;
use App\Effects\Effect;
use App\Effects\RestoreEffect;

class AssassinDefinition extends PersonDefinition
{
    public string $title = 'Assassin';
    public string $description = 'Silent but deadly';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [
            new BaseAbility(new AssassinAbility(), [
                /** No special rules for availability */
            ])
        ];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
}