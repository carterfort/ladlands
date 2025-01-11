<?php

namespace App\Abilities;

use App\Effects\GunnerEffect;
use App\Targeting\TargetType;

class GunnerAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Gunner",
            $description = "Description for Gunner ability",
            $cost = 1,
            $targetRequirements = [TargetType::OPPONENT, TargetType::UNPROTECTED],
            $effect = GunnerEffect::class
        );
    }
}