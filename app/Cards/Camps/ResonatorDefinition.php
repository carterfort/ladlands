<?php

namespace App\Cards\Camps;

use App\Abilities\DamageAbility;
use App\Abilities\ResonatorAbility;

class ResonatorDefinition extends CampDefinition
{
    public string $title = 'Resonator';
    public string $description = 'Hit em!';
    public int $drawCount = 2;

    public function getBaseAbilities(): array
    {
        return [new ResonatorAbility()];
    }
}
