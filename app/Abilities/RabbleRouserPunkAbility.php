<?php

namespace App\Abilities;

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
