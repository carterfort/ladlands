<?php

namespace App\Abilities;

use App\Effects\DestroyEffect;
use App\Targeting\TargetType;

class AssassinAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Assassinate",
            $description = "Destroy an unprotected person",
            $cost = 2,
            $targetRequirements = [
                TargetType::OPPONENT,
                TargetType::UNPROTECTED,
                TargetType::PERSON,
                TargetType::BATTLEFIELD
            ],
            $effectClass = DestroyEffect::class
        );
    }
}