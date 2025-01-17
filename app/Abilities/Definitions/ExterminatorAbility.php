<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\ExterminatorEffect;

class ExterminatorAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Exterminator",
            $description = "Destroy all damaged enemies",
            $cost = 2,
            $effectClasses = [ExterminatorEffect::class]
        );
    }
}