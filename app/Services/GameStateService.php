<?php

namespace App\Services;

use App\Abilities\Ability;
use App\Cards\HasAbilities;
use App\Models\{Game, Player, Card, GameBoard, GameBoardSpace};
use App\Targeting\TargetResolver;
use Illuminate\Support\Collection;

class GameStateService
{

    protected Game $game;
    protected Collection $abilities;

    public function __construct(
        private readonly CardLocationManager $locationManager,
        public readonly GameStateChangeService $stateChanger,
        public readonly TargetResolver $targetResolver,
    ) {
        $this->abilities = collect([]);
    }

    public function setGame(Game $game){
        $this->game = $game;
    }

    public function putCardInSpace(Card $card, GameBoardSpace $space){
        $card->location = [
            'space_id' => $space->id,
            'type' => 'BATTLEFIELD'
        ];
        $card->save();
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

}