<?php

namespace App\Models;

use App\Abilities\Ability;
use App\Cards\Camps\CampDefinition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Cards\CardDefinition;
use App\Cards\Events\EventDefinition;
use App\Cards\People\PeopleDefinition;
use App\Cards\People\PersonDefinition;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;

    protected $fillable = ['card_definition', 'location', 'is_ready'];

    protected $casts = [
        'location' => 'object',
        'is_damaged' => 'boolean',
        'is_ready' => 'boolean',
        'is_flipped' => 'boolean'
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

    public function getOwner(){
        if ($this->location->type == 'player_hand'){
            return Player::find($this->location->player_id);
        }

        if ($this->location->space_id != null){
            return GameBoardSpace::find($this->location->space_id)->board->player;
        }
    }

    public function scopeName($query, $name){
        $query->where('card_definition', 'LIKE', $name.'Definition');
    }

    protected ?CardDefinition $definitionInstance = null;

    public function getDefinition(): PersonDefinition|CampDefinition|EventDefinition
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
