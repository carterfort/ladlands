<?php

namespace App\Cards\People;

use App\Abilities\DamageAbility;
use App\Abilities\LootAbility;

class LooterDefinition extends PersonDefinition {

    public string $title = 'Looter';
    public string $description = 'Freeeeeddooooooomm!!!';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [new LootAbility()];
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new DamageAbility();   
    }

}