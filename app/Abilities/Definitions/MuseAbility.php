<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\AddWaterEffect;

class MuseAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Come, drink of my inspiration",
            $description = "Add 1 water",
            $cost = 0,
            $effectClasses = [AddWaterEffect::class]
        );
    }
}