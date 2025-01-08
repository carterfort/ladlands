<?php

namespace App\Abilities;

abstract class Ability {
    
    public function __construct(
        public string $title,
        public string $description,
        public int $cost,
        public array $targetTypes,
        public string $effectClass
    ) {}
}