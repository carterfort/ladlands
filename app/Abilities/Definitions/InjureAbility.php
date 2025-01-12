<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\InjureEffect;

class  InjureAbility extends Ability {

    public function __construct($cost = 1) {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected person",
            $cost = $cost,
            $effectClasses = [InjureEffect::class]
        );
    }
}