<?php

namespace App\Abilities;

use App\Effects\DamageEffect;
use App\Targeting\TargetType;

class LootAbility extends Ability {

    public function __construct() {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected card. Draw a card if it's a camp.",
            $cost = 2,
            $targetRequirements = [TargetType::OPPONENT, TargetType::UNPROTECTED, TargetType::BATTLEFIELD],
            $effect = DamageEffect::class
        );
    }
}