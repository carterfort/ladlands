<?php

namespace App\Models;

use App\Abilities\Ability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Cards\CardDefinition;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;

    protected $casts = [
        'location' => 'object'
    ];

    protected ?CardDefinition $definitionInstance = null;

    public function getDefinition(): CardDefinition
    {
        if (!$this->definitionInstance) {
            $definitionClass = $this->card_definition;
            $this->definitionInstance = new $definitionClass();
        }
        return $this->definitionInstance;
    }

    public function getTitle(): string 
    {
        return $this->getDefinition()->title;
    }

    public function getDescription(): string 
    {
        return $this->getDefinition()->description;
    }

    public function getWaterCost(): int 
    {
        return $this->getDefinition()->waterCost;
    }

    public function getAvailableAbilities(): array 
    {
        if ($this->face_down || !$this->is_ready) {
            return [];
        }
        return $this->getDefinition()->abilities;
    }

    public function getJunkAbility(): Ability
    {
        return $this->getDefinition()->junkAbility;
    }
}
