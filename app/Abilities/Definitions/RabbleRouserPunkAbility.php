<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\GainPunkEffect;

class RabbleRouserPunkAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Recruit",
            $description = "Gain a punk",
            $cost = 1,
            $effectClasses = [GainPunkEffect::class]
        );
    }
}
