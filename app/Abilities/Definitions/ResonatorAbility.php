<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\ResonatorEffect;

class ResonatorAbility extends Ability
{

    public function __construct($cost = 0)
    {
        parent::__construct(
            $title = "Sound the alarm!!!",
            $description = "Destroy an unprotected, damaged card",
            $cost = 2,
            $effectClasses = [ResonatorEffect::class]
        );
    }
}
