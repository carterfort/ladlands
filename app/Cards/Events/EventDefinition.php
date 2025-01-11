<?php

namespace App\Cards\Events;

use App\Cards\CardDefinition;

abstract class EventDefinition extends CardDefinition {
    public string $title = '';
    public string $description = '';
    public int $waterCost = 1;
    public int $baseTimer = 1;
    public string $type = "Event";

    abstract public function calculateBaseTimer();

}