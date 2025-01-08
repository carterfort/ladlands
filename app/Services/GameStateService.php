<?php

namespace App\Services;

use App\Abilities\Ability;
use App\Models\{Game, Player, Card, GameBoard, GameBoardSpace};
use App\Targeting\TargetResolver;
use Illuminate\Support\Collection;

class GameStateService
{

    protected Game $game;

    public function __construct(
        private readonly CardLocationManager $locationManager,
        public readonly GameStateChangeService $stateChanger,
        public readonly TargetResolver $targetResolver,
    ) {}

    public function setGame(Game $game){
        $this->game = $game;
    }

    public function getStateForPlayer(Player $player): array
    {
        $playerBoard = GameBoard::wherePlayerId($player->id)->whereGameId($this->game->id)->first();
        $opponent = $this->getOpponentForPlayer($this->game, $player);
        $opponentBoard = GameBoard::wherePlayerId($opponent->id)->whereGameId($this->game->id)->first();

        return [];

        // To be implemented 

        // return [
        //     'player' => [
        //         'id' => $player->id,
        //         'water' => $player->water,
        //         'hand' => $this->getPlayerHand($this->game, $player),
        //         'board' => $this->getBoardState($playerBoard)
        //     ],
        //     'opponent' => [
        //         'id' => $opponent->id,
        //         'water' => $opponent->water,
        //         'hand_size' => $this->getHandSize($this->game, $opponent),
        //         'board' => $this->getBoardState($opponentBoard)
        //     ],
        //     'game' => [
        //         'id' => $this->game->id,
        //         'current_player_id' => $this->game->current_player_id,
        //         'status' => $this->game->status,
        //         'punk_deck_size' => $this->getDeckSize($this->game, 'PUNK_DECK'),
        //         'discard_deck_size' => $this->getDeckSize($this->game, 'DISCARD_DECK')
        //     ],
        //     'pending_input_requests' => $this->getPendingInputRequests($this->game, $player)
        // ];
    }

    private function getOpponentForPlayer(Game $game, Player $player){
        if ($game->playerA->id == $player->id){
            return $game->playerOne;
        }

        return $game->playerA;
    }

    public function getValidTargetsForAbility(
        Ability $ability,
        Player $player
    ): Collection {
        $targetTypes = $ability->targetTypes;
        return $this->targetResolver->getValidTargets($player, $targetTypes, Card::whereGameId($this->game->id)->get()->groupBy('location.space_id'));
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