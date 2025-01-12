<?php

namespace App\Cards\People;

use App\Cards\CardDefinition;
use App\Cards\HasAbilities;
use App\Cards\HasJunkEffect;
use App\Effects\Effect;

abstract class PersonDefinition extends CardDefinition implements HasAbilities, HasJunkEffect
{

    public string $title = '';
    public string $description = '';
    public int $waterCost = 1;
    public string $type = "Person";
    public array $abilities = [];
    public Effect $junkEffect;

    abstract public function getBaseAbilities(): array;
    abstract public function getJunkEffect(): Effect;

    public function calculateCastingCost(): int {
        return $this->waterCost;
    }
}
