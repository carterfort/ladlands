<?php

namespace App\Cards\People;

use App\Abilities\DamageAbility;
use App\Abilities\RabbleRouserPunkAbility;
use App\Effects\RaidEffect;
use App\Effects\Effect;

class RabbleRouserDefinition extends PersonDefinition
{
    public string $title = 'Rabble Rouser';
    public string $description = 'Lead the rebellion';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        $abilities = [new RabbleRouserPunkAbility()];
        // Check to be sure that the controlling player
        // has at least one punk on their board.
        // If they do, add the DamageAbility
        return $abilities;
    }

    public function getJunkEffect(): Effect
    {
        return new RaidEffect();
    }
}