<?php

namespace App\Cards\Perma;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\WaterSiloAbility;
use App\Cards\CardDefinition;
use App\Cards\HasAbilities;
use App\Cards\HasJunkEffect;
use App\Effects\AddWaterEffect;
use App\Effects\Effect;

class WaterSiloDefinition extends CardDefinition implements 
    HasPermanentSpace, HasAbilities, HasJunkEffect
{
    public string $title = 'Water Silo';
    public string $description = 'A renewable source of water';
    public string $type = 'Perma';
    public int $permaPosition = 1;

    public function getPermanentSpacePosition(): int
    {
        return $this->permaPosition;
    }

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new WaterSiloAbility())];
    }

    public function getJunkEffect(): Effect
    {
        return new AddWaterEffect();
    }
}