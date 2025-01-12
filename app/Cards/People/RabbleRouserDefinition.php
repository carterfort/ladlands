<?php

namespace App\Cards\People;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\DamageAbility;
use App\Abilities\Definitions\RabbleRouserPunkAbility;
use App\Abilities\Rules\HasPunksInPlayRule;
use App\Effects\RaidEffect;
use App\Effects\Effect;

class RabbleRouserDefinition extends PersonDefinition
{
    public string $title = 'Rabble Rouser';
    public string $description = 'Lead the rebellion';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [
            new BaseAbility(new RabbleRouserPunkAbility()),
            new BaseAbility(new DamageAbility(), [new HasPunksInPlayRule()])
        ];
    }

    public function getJunkEffect(): Effect
    {
        return new RaidEffect();
    }
}