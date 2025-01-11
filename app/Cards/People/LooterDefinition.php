<?php

namespace App\Cards\People;

use App\Abilities\DamageAbility;

class LooterDefinition extends PersonDefinition {

    public string $title = 'Looter';
    public string $description = 'Freeeeeddooooooomm!!!';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [new DamageAbility(2)];
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new DamageAbility();   
    }

}