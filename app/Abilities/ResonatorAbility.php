<?php

namespace App\Abilities;

use App\Effects\DestroyEffect;
use App\Targeting\TargetType;

class ResonatorAbility extends Ability
{

    public function __construct($cost = 0)
    {
        parent::__construct(
            $title = "Sound the alarm!!!",
            $description = "Destroy an unprotected, damaged card",
            $cost = $cost,
            $targetRequirements = [TargetType::OPPONENT, TargetType::UNPROTECTED, TargetType::BATTLEFIELD, TargetType::DAMAGED],
            $effectClass = DestroyEffect::class
        );
    }
}
