<?php

namespace App\Services;

use App\Abilities\Ability;
use App\Cards\HasAbilities;
use App\Effects\ApplyToPlayerImmediatelyEffect;
use App\Effects\Effect;
use App\Effects\InputDependentEffect;
use App\Models\{Game, Player, Card, GameBoardSpace, PlayerInputRequest};
use App\Targeting\TargetResolver;
use Illuminate\Support\Collection;

class GameStateService
{

    public readonly Game $game;
    protected Collection $abilities;

    public function __construct(
        private readonly DeckBuildingService $deckBuilder,
        private readonly AbilityHandlerService $abilityHandler,
        public readonly GameStateChangeService $stateChanger,
        public readonly TargetResolver $targetResolver,
        public readonly EventHandlerService $eventHandler,
    ) {
        $this->abilities = collect([]);
    }

    public function setGame(Game $game){
        $this->game = $game;
    }

    public function endTurn(){
        $this->stateChanger->endTurn($this);
    }

    public function startTurn(){
        $this->stateChanger->startTurnForPlayer($this, $this->game->currentPlayer->getOpponent());

        $eventQueueSpaces = $this->game->currentPlayer->board->spaces()->type('EVENT')->pluck('id');
        
        $this->getGameCardsQuery()
            ->where('location->type', 'event_queue')
            ->whereIn('location->space_id', $eventQueueSpaces)
            ->each(function (Card $event){
                $position = GameBoardSpace::findOrFail($event->location->space_id)->position;
                if ($position == 1) {
                    $effects = $event->getDefinition()->getEventEffects();
                    foreach($effects as $effect){
                        $this->applyEventEffect($effect);
                    }
                }
            });
    }

    public function buildDecks(){
        $this->deckBuilder->buildDecks($this->game);
    }

    public function getGameCardsQuery(){
        return $this->game->cards();
    }

    public function applyEventEffect(Effect $effect){
        
    }


    public function applyEffect($effect, ?PlayerInputRequest $request, ?Player $player)
    {
        $implements = collect(class_implements($effect));
        when(
            $implements->contains(InputDependentEffect::class) && $request,
                fn() => $effect->applyWithInput($this, $request),
            $implements->contains(ApplyToPlayerImmediatelyEffect::class),
                fn() => $effect->applyEffect($this, $player)
        );
    }

    public function advanceEventInQueue(Card $event)
    {
        if ($event->location->position = 0) {
            $effect = $event->definition->getEventEffect();
            $this->applyEffect($effect, NULL, $event->owner);
        }
    }


    public function handleInputRequestResponse(PlayerInputRequest $request): void {
        
        // Will we want to add logging here? Or is that in the StateChangeService?

        $effects = app('effects')->get($request->effect_key);
        foreach($effects as $effect){
            $this->applyEffect($effect, $request, NULL);
        }
    }

    protected function checkForValidTargetsForEffects(array $effects, $card, $cardsState): bool {
        $hasValidTargets = true;
        // Check each effect has valid targets
        foreach ($effects as $effect) {
            $targetTypes = $effect->getTargetingRequirements();
            if (empty($targetTypes)) {
                continue;
            }

            $validSpaces = $this->targetResolver->getValidGameboardSpaces($card->getOwner(), $targetTypes, $cardsState);

            if ($validSpaces->isEmpty()) {
                $hasValidTargets = false;
                break;
            }
        }
        return $hasValidTargets;
    }

    public function applyAbilitiesForCardsInPlay()
    {
        $cardsInPlay = $this->game->cards()->whereIn('location->type', ['BATTLEFIELD'])->get();
        $cardsState = Card::whereGameId($this->game->id)->get()->groupBy('location.space_id');

        $cardsInPlay->each(function (Card $card) use ($cardsState) {
            $definition = $card->getDefinition();
            if (!($definition instanceof HasAbilities)) {
                return;
            }

            $baseAbilities = $definition->getBaseAbilities();
            $validatedAbilities = [];

            foreach ($baseAbilities as $ability) {
                $hasValidTargets = $this->checkForValidTargetsForEffects(
                    app('effects')->get($ability->effectClasses), $card, $cardsState);

                if ($hasValidTargets) {
                    $validatedAbilities[] = $ability;
                }
            }

            if (!empty($validatedAbilities)) {
                $this->abilities[$card->id] = $validatedAbilities;
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
        $effects = app('effects')->get($ability->effectClasses);
        
        $validSpaces = collect([]);
        foreach ($effects as $effect){
            $targetTypes = $effect->getTargetingRequirements();
            $cardsState = Card::whereGameId($this->game->id)->get()->groupBy('location.space_id');
            $validSpaces[$effect::class] = $this->targetResolver->getValidGameboardSpaces($player, $targetTypes, $cardsState);
        }

        return $validSpaces;
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