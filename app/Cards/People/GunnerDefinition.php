<?php

namespace App\Cards\People;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\GunnerAbility;
use App\Effects\Effect;
use App\Effects\RestoreEffect;

class GunnerDefinition extends PersonDefinition
{
    public string $title = 'Gunner';
    public string $description = 'Spray and pray';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [
            new BaseAbility(
                new GunnerAbility()
            )
        ];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
}