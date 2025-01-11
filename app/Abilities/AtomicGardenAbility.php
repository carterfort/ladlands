<?php

namespace App\Cards\Camps;

use App\Abilities\AtomicGardenAbility;

class AtomicGardenDefinition extends CampDefinition
{
    public string $title = 'Atomic Garden';
    public string $description = 'Restore your damaged people';
    public int $drawCount = 1;

    public function getBaseAbilities(): array
    {
        return [new AtomicGardenAbility()];
    }
}