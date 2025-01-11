<?php

namespace App\Abilities;

use App\Effects\ExterminatorEffect;
use App\Targeting\TargetType;

class ExterminatorAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Exterminator",
            $description = "Destroy all damaged enemies",
            $cost = 2,
            $targetRequirements = [], // This doesn't require player input
            $effectClass = ExterminatorEffect::class
        );
    }
}