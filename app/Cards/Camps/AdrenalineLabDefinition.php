<?php

namespace App\Cards\Camps;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\AdrenalineLabAbility;

class AdrenalineLabDefinition extends CampDefinition
{
    public string $title = 'AdrenalineLab';
    public string $description = 'Description for AdrenalineLab';
    public int $waterCost = 1;

        public function getBaseAbilities(): array
    {
        return [new BaseAbility(new AdrenalineLabAbility())];
    }
}