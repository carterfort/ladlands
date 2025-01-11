<?php

namespace App\Cards\Camps;

use App\Abilities\AdrenalineLabAbility;

class AdrenalineLabDefinition extends CampDefinition
{
    public string $title = 'AdrenalineLab';
    public string $description = 'Description for AdrenalineLab';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new AdrenalineLabAbility()];
    }
}