<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\AtomicGardenEffect;

class AtomicGardenAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Atomic Growth",
            $description = "Restore a damaged person. They become ready",
            $cost = 2,
            $effectClasses = [AtomicGardenEffect::class]
        );
    }
}