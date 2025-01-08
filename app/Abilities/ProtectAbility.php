<?php

namespace App\Abilities;

use App\Effects\ProtectEffect;
use App\Targeting\TargetType;

// New ability for the Guard
class ProtectAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Protect",
            $description = "Protect this card from damage",
            $cost = 1,
            $targetRequirements = [TargetType::YOU],
            $effect = ProtectEffect::class
        );
    }
}
