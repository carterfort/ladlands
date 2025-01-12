<?php

namespace App\Abilities;

class BaseAbility
{
    public function __construct(
        public readonly Ability $ability,
        /** AvailabilityRule array */
        public readonly array $rules = []
    ) {}
}