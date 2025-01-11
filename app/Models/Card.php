<?php

namespace App\Models;

use App\Abilities\Ability;
use App\Cards\Camps\CampDefinition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Cards\CardDefinition;
use App\Cards\People\PeopleDefinition;
use App\Cards\People\PersonDefinition;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;

    protected $fillable = ['card_definition', 'location'];

    protected $casts = [
        'location' => 'object',
        'is_damaged' => 'boolean',
        'is_ready' => 'boolean'
    ];

    public function game() : BelongsTo{
        return $this->belongsTo(Game::class);
    }

    public function scopePunkDeck($query){
        $query->where('location->type', 'punk_deck');
    }

    public function scopeDiscardDeck($query)
    {
        $query->where('location->type', 'discard_deck');
    }

    public function scopeCampDeck($query)
    {
        $query->where('location->type', 'camp_deck');
    }

    protected ?CardDefinition $definitionInstance = null;

    public function getDefinition(): PersonDefinition|CampDefinition
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
}
