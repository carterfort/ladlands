<?php

namespace App\Abilities;

use App\Effects\LootEffect;

class LootAbility extends Ability {

    public function __construct() {
        parent::__construct(
            $title = "Damage",
            $description = "Damage target unprotected card. Draw a card if it's a camp.",
            $cost = 2,
            $effectClass = LootEffect::class
        );
    }
}