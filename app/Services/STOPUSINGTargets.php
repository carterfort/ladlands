<?php

namespace App\Service;

use App\Models\Player;

/**
 * Fluent targeting system for card game effects
 * 
 * This system allows composing complex targeting rules using a fluent interface.
 * The Targets class builds up filter criteria which are then resolved by the 
 * TargetResolver into an array of valid target IDs.
 * 
 * Examples:
 * 
 * 1. Get unprotected enemy person cards:
 *    Targets::opponent()->unprotected()->person();
 *    // Returns IDs of person cards on opponent's board that have no undamaged cards
 *    // in front of them
 * 
 * 2. Get your own damaged cards of any type:
 *    Targets::you()->damaged()->any();
 *    // Returns IDs of all your damaged cards, regardless of type (camp or person)
 * 
 * 3. Get empty spaces on your battlefield:
 *    Targets::you()->empty()->any();
 *    // Returns IDs of empty spaces on your battlefield
 */

class Targets
{
    public readonly Player $player;
    public readonly string $cardType;
    public readonly bool $unprotectedOnly;
    public readonly bool $damagedOnly;
    public readonly bool $emptyOnly;
    public readonly bool $cardsInHand;

    public static function target(Player $player): self {
        $instance = new self();
        $instance->player = $player;
        return $instance;
    }


    public function unprotected(): self
    {
        $this->unprotectedOnly = true;
        return $this;
    }

    public function damaged(): self
    {
        $this->damagedOnly = true;
        return $this;
    }

    public function empty(): self
    {
        $this->emptyOnly = true;
        return $this;
    }

    public function person(): self
    {
        $this->cardType = 'person';
        return $this;
    }

    public function camp(): self
    {
        $this->cardType = 'camp';
        return $this;
    }

    public function any(): self
    {
        return $this;
    }

    public function cardsInHand(): self
    {
        $this->cardsInHand = true;
        return $this;
    }

}