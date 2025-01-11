<?php

namespace App\Services;

use App\Abilities\Ability;
use App\Cards\HasAbilities;
use App\Effects\InputDependentEffect;
use App\Models\{Game, Player, Card, PlayerInputRequest};
use App\Targeting\TargetResolver;
use Illuminate\Support\Collection;

class GameStateService
{

    protected Game $game;
    protected Collection $abilities;

    public function __construct(
        private readonly DeckBuildingService $deckBuilder,
        private readonly AbilityHandlerService $abilityHandler,
        public readonly GameStateChangeService $stateChanger,
        public readonly TargetResolver $targetResolver,
    ) {
        $this->abilities = collect([]);
    }

    public function setGame(Game $game){
        $this->game = $game;
    }

    public function buildDecks(){
        $this->deckBuilder->buildDecks($this->game);
    }

    public function getGameCardsQuery(){
        return $this->game->cards();
    }

    public function handleInputRequestResponse(PlayerInputRequest $request): void {
        
        // Will we want to add logging here? Or is that in the StateChangeService?

        $effect = app('effects')->get($request->effect_key);
        $implements = collect(class_implements($effect));
        when(
            $implements->contains(InputDependentEffect::class),
                fn() => $effect->applyWithInput($this, $request)
        );
    }

    public function applyAbilitiesForCardsInPlay(){

        $cardsInPlay = $this->game->cards()->whereIn('location->type',['BATTLEFIELD'])->get();
        $cardsInPlay->each(function(Card $card){
            $definition = $card->getDefinition();
            if ($definition instanceof HasAbilities) {
                $this->abilities[$card->id] = $definition->getBaseAbilities();
            }
        });
    }

    public function getAbilitiesForCard(Card $card){
        return $this->abilities->get($card->id) ?? [];
    }

    public function getValidTargetsForAbility(
        Ability $ability,
        Player $player
    ): Collection {
        $targetTypes = $ability->targetTypes;

        $cardsState = Card::whereGameId($this->game->id)->get()->groupBy('location.space_id');
        return $this->targetResolver->getValidGameboardSpaces($player, $targetTypes, $cardsState);
    }

    private function getDeckSize(Game $game, string $deckType): int
    {
        return $game->cardLocations()
            ->where('location_type', $deckType)
            ->count();
    }

    private function getPendingInputRequests(Game $game, Player $player): Collection
    {
        return $game->playerInputRequests()
            ->where('player_id', $player->id)
            ->where('status', 'pending')
            ->get()
            ->map(fn($request) => [
                'id' => $request->id,
                'effect_key' => $request->effect_key,
                'valid_targets' => $request->valid_targets,
                'required_target_count' => $request->required_target_count,
                'source_card_id' => $request->source_card_id
            ]);
    }

    public function playerActivatesAbilityViaCard(Player $player, Ability $ability, Card $card){
        // Check to see if the player can activate this ability

        // Validate that this card can activate this ability in the current game state
        
        // Delegate handling
        $this->abilityHandler->activateAbilityByPlayer($this, $ability, $player);

        // Resolve anything else?
    }

}