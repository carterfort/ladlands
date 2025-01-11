<?php

namespace App\Effects;


interface Effect {

    public function getTargetingRequirements(): array;

}