<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\DamageEffect;

class DamageAbility extends Ability {

    public function __construct($cost = 0) {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected card",
            $cost = $cost,
            $effectClasses = [DamageEffect::class]
        );
    }
}