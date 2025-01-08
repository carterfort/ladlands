<?php

namespace App\Cards;

use App\Abilities\Ability;

abstract class CardDefinition
{
    public string $title;
    public string $description;
    public int $waterCost;
    public array $abilities = [];
    public Ability $junkAbility;

    abstract public function registerAbilities(): void;
    abstract public function registerJunkAbility(): void;

}