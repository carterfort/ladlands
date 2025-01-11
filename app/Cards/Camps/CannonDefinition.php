<?php

namespace App\Cards\Camps;

use App\Abilities\CannonAbility;

class CannonDefinition extends CampDefinition
{
    public string $title = 'Cannon';
    public string $description = 'A powerful but unstable weapon';
    public int $drawCount = 1;

    public function getBaseAbilities(): array
    {
        return [new CannonAbility()];
    }
}