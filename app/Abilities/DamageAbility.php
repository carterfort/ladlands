<?php

namespace App\Abilities;

use App\Effects\DamageEffect;
use App\Targeting\TargetType;

class DamageAbility extends Ability {

    public function __construct($cost = 0) {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected card",
            $cost = $cost,
            $targetRequirements = [TargetType::OPPONENT, TargetType::UNPROTECTED],
            $effect = DamageEffect::class
        );
    }
}