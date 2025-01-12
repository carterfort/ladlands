<?php

namespace App\Cards;

interface HasOngoingEffects {
    public function getOngoingEffects(): array;
}