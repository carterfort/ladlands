<?php

namespace App\Abilities\Definitions;

use App\Abilities\Ability;
use App\Effects\BloodBankEffect;

class BloodBankAbility extends Ability
{
    public function __construct()
    {
        parent::__construct(
            $title = "Blood Donation",
            $description = "Destroy one of your people. Then gain water.",
            $cost = 1,
            $effectClasses = [BloodBankEffect::class]
        );
    }
}