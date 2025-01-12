<?php

namespace App\Cards\Camps;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\ResonatorAbility;

class ResonatorDefinition extends CampDefinition
{
    public string $title = 'Resonator';
    public string $description = 'Hit em!';
    public int $drawCount = 2;

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new ResonatorAbility())];
    }
}
