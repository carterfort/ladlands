<?php

namespace App\Cards\People;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\PyromaniacAbility;
use App\Effects\Effect;
use App\Effects\RestoreEffect;

class PyromaniacDefinition extends PersonDefinition
{
    public string $title = 'Pyromaniac';
    public string $description = 'Some people just want to watch the world burn';
    public int $waterCost = 1;

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new PyromaniacAbility())];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
}