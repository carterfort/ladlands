<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
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