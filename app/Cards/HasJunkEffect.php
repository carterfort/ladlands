<?php

namespace App\Cards;

use App\Effects\Effect;

interface HasJunkEffect {
    public function getJunkEffect(): Effect;
}