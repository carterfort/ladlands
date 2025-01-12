<?php

namespace App\Cards\Camps;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\BloodBankAbility;

class BloodBankDefinition extends CampDefinition
{
    public string $title = 'Blood Bank';
    public string $description = 'Sacrifice for sustenance';
    public int $drawCount = 1;

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new BloodBankAbility())];
    }
}