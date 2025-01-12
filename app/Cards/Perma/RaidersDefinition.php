<?php

namespace App\Cards\Perma;

use App\Cards\Events\EventDefinition;
use App\Effects\Effect;
use App\Effects\RaidersEffect;
use Error;

class RaidersDefinition extends EventDefinition implements HasPermanentSpace
{
    public string $title = 'Raiders';
    public string $description = 'Damage a camp of the opponent\'s choice. Then, return this card to your play area';
    public int $permaPosition = 2;

    public function getPermanentSpacePosition(): int
    {
        return $this->permaPosition;
    }

    public function getEventEffects(): array
    {
        return [new RaidersEffect()];
    }

    public function getJunkEffect(): Effect
    {
        throw new Error("Raiders can't be junked, and probably shouldn't be in your hand.");
    }
}