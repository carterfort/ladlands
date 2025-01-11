<?php

namespace App\Cards\People;

use App\Abilities\MuseAbility;

class MuseDefinition extends PersonDefinition
{
    public string $title = 'Muse';
    public string $description = 'Description for Muse';
    public int $waterCost = 1;
            
    public function getBaseAbilities(): array
    {
        return [new MuseAbility()];
    }

    public function registerJunkAbility(): void
    {
        $this->junkAbility = new MuseAbility();
    }
}