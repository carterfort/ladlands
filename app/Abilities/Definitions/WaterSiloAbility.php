<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\WaterSiloEffect;

class WaterSiloAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Retrieve Silo",
            $description = "Put this card in your hand",
            $cost = 1,
            $effectClasses = [WaterSiloEffect::class]
        );
    }
}