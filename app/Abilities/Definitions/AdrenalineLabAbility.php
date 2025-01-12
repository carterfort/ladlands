<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\AdrenalineLabEffect;

class AdrenalineLabAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Last Gasp",
            $description = "Use the ability of any one of your damaged people (you must still pay). Then, destroy it",
            $cost = 0,
            $effectClasses = [AdrenalineLabEffect::class]
        );
    }
}