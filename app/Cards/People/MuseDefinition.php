<?php

namespace App\Cards\People;

use App\Abilities\MuseAbility;
use App\Effects\AddWaterEffect;
use App\Effects\Effect;

class MuseDefinition extends PersonDefinition
{
    public string $title = 'Muse';
    public string $description = 'Description for Muse';
    public int $waterCost = 1;
            
    public function getBaseAbilities(): array
    {
        return [new MuseAbility()];
    }

    public function getJunkEffect(): Effect
    {
        return new AddWaterEffect();
    }
}