<?php

namespace App\Cards;

interface HasAbilities {
    /** An array of BaseAbility classes */
    public function getBaseAbilities() : array;
}