<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\PyromaniacEffect;

class PyromaniacAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Ignite",
            $description = "Set fire to an unprotected enemy camp",
            $cost = 1,
            $effectClasses = [PyromaniacEffect::class]
        );
    }
}