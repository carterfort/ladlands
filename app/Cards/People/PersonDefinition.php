<?php

namespace App\Cards\People;

use App\Abilities\Ability;
use App\Cards\CardDefinition;
use App\Cards\HasAbilities;

abstract class PersonDefinition extends CardDefinition implements HasAbilities
{

    public string $title = '';
    public string $description = '';
    public int $waterCost = 1;
    public string $type = "Person";
    public array $abilities = [];
    public Ability $junkAbility;

    abstract public function getBaseAbilities(): array;
    abstract public function registerJunkAbility(): void;
    
    public function calculateCastingCost(): int {
        return $this->waterCost;
    }
}
