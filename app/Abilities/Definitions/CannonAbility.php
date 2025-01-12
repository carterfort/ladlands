<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\CannonEffect;

class CannonAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Fire the Cannon",
            $description = "Then, damage this card",
            $cost = 1,
            $effectClasses = [CannonEffect::class]
        );
    }
}