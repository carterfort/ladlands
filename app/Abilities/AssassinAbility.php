<?php

namespace App\Abilities;

use App\Effects\AssassinEffect;

class AssassinAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Assassinate",
            $description = "Destroy an unprotected person",
            $cost = 2,
            $effectClass = AssassinEffect::class
        );
    }
}