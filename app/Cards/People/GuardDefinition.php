<?php

namespace App\Cards\People;

use App\Abilities\DamageAbility;
use App\Abilities\ProtectAbility;
use App\Cards\CardDefinition;

class GuardDefinition extends CardDefinition
{
    public string $title = 'Guard';
    public string $description = 'Protects your territory';
    public int $waterCost = 2;
    public string $type = "Person";

    public function registerAbilities(): void
    {
        $this->abilities[] = new ProtectAbility();
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new DamageAbility();
    }
}
