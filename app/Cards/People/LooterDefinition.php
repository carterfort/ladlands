<?php

namespace App\Cards\People;

use App\Abilities\DamageAbility;
use App\Cards\CardDefinition;

class LooterDefinition extends CardDefinition {

    public string $title = 'Looter';
    public string $description = 'Freeeeeddooooooomm!!!';
    public int $waterCost = 1;
    public string $type = "Person";

    public function registerAbilities(): void
    {
        $this->abilities[] = new DamageAbility(2);
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new DamageAbility();   
    }

}