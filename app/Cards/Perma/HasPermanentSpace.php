<?php

namespace App\Cards\Perma;

interface HasPermanentSpace {
    public function getPermanentSpacePosition(): int;
}