<?php

namespace App\Abilities;

use App\Effects\GunnerEffect;

class GunnerAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Gunner",
            $description = "Description for Gunner ability",
            $cost = 1,
            $effectClasses = [GunnerEffect::class]
        );
    }
}