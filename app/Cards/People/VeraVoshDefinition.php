<?php

namespace App\Cards\People;

use App\Abilities\BaseAbility;
use App\Abilities\Definitions\InjureAbility;
use App\Cards\HasOngoingEffects;
use App\Effects\Effect;
use App\Effects\GainPunkEffect;
use App\Effects\OngoingEffects\KeepReadyAfterFirstUse;

class VeraVoshDefinition extends PersonDefinition implements HasOngoingEffects
{
    public string $title = 'Vera Vosh';
    public string $description = 'Stick with me, kid. I can show you some moves.';
    public int $waterCost = 3;

    public function getBaseAbilities(): array
    {
        return [
            new BaseAbility(new InjureAbility())
        ];
    }

    public function getJunkEffect(): Effect
    {
        return new GainPunkEffect();
    }

    public function getOngoingEffects(): array
    {
        return [new KeepReadyAfterFirstUse()];
    }
}
