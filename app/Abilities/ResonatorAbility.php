<?php

namespace App\Abilities;

use App\Effects\ResonatorEffect;

class ResonatorAbility extends Ability
{

    public function __construct($cost = 0)
    {
        parent::__construct(
            $title = "Sound the alarm!!!",
            $description = "Destroy an unprotected, damaged card",
            $cost = $cost,
            $effectClass = ResonatorEffect::class
        );
    }
}
