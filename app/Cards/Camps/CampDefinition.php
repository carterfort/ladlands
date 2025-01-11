<?php

namespace App\Cards\Camps;

use App\Cards\CardDefinition;
use App\Cards\HasAbilities;

abstract class CampDefinition extends CardDefinition implements HasAbilities {
    
    public string $title = '';
    public string $description = '';
    public int $drawCount = 1;
    public string $type = "Camp";
    public array $abilities = [];

    abstract public function getBaseAbilities(): array;
}