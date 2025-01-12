<?php

namespace App\Cards\Events;

use App\Effects\BanishEffect;
use App\Effects\Effect;
use App\Effects\RaidEffect;

class BanishDefinition extends EventDefinition
{
    public string $title = 'Banish';
    public string $description = 'Send me on way.';
    public int $waterCost = 1;
    public int $baseTimer = 1;

    public function getEventEffects(): array
    {
        return [new BanishEffect()];
    }

    public function getJunkEffect(): Effect
    {
        return new RaidEffect();
    }

    
}