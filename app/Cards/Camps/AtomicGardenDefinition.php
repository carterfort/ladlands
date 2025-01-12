<?php

namespace App\Cards\Camps;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\AtomicGardenAbility;

class AtomicGardenDefinition extends CampDefinition
{
    public string $title = 'Atomic Garden';
    public string $description = 'Restore your damaged people';
    public int $drawCount = 1;

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new AtomicGardenAbility())];
    }
}