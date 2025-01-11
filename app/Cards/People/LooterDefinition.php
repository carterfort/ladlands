<?php

namespace App\Cards\People;

use App\Abilities\LootAbility;
use App\Effects\AddWaterEffect;
use App\Effects\Effect;

class LooterDefinition extends PersonDefinition {

    public string $title = 'Looter';
    public string $description = 'Freeeeeddooooooomm!!!';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [new LootAbility()];
    }

    public function getJunkEffect(): Effect
    {
        return new AddWaterEffect();
    }

}