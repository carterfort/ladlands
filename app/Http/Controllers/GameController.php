<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Card;
use App\Models\GameBoardSpace;
use App\Services\GameStateBuilderService;
use App\Services\GameStateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    public function __construct(
        private GameStateService $gameState,
        private GameStateBuilderService $gameStateBuilder
    ) {}

    public function index(Request $request): JsonResponse
    {
        $games = Game::whereHas('players', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })->get();

        return response()->json($games);
    }

    public function store(Request $request): JsonResponse
    {
        $game = Game::create();

        // Set up initial game state
        $this->gameState->setGame($game);
        $this->gameState->buildDecks();
        $this->gameState->placePermaCards();

        return response()->json($game, 201);
    }

    public function endTurn(Game $game): JsonResponse
    {
        $this->gameState->setGame($game);
        $this->gameState->endTurn();

        return response()->json(['message' => 'Turn ended']);
    }

    public function getState(Game $game): JsonResponse
    {
        $this->gameState->setGame($game);
        $gameState = $this->gameStateBuilder
            ->forGame($game)
            ->forRequestingPlayer($game->currentPlayer)
            ->build();

        return response()->json($gameState->toArray());
    }

    public function playPersonAtCampGameboardSpace(Request $request, Game $game): JsonResponse
    {
        $this->gameState->setGame($game);

        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'space_id' => 'required|exists:game_board_spaces,id'
        ]);

        $card = Card::findOrFail($request->card_id);
        $space = GameBoardSpace::findOrFail($request->space_id);

        $this->gameState->stateChanger->putCardInSpace($card, $space);

        return response()->json(['message' => 'Card played successfully']);
    }

    public function addEventToQueue(Request $request, Game $game): JsonResponse
    {
        $this->gameState->setGame($game);

        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'queue_position' => 'required|integer|min:0|max:2'
        ]);

        $card = Card::findOrFail($request->card_id);

        $card->location = [
            'type' => 'EVENT_QUEUE',
            'position' => $request->queue_position,
            'player_id' => $request->user()->id
        ];
        $card->save();

        return response()->json(['message' => 'Event added to queue']);
    }

    public function payToDraw(Game $game): JsonResponse
    {
        $this->gameState->setGame($game);

        $player = $game->currentPlayer;

        if ($player->water < 2) {
            return response()->json(['error' => 'Not enough water'], 400);
        }

        $player->water -= 2;
        $player->save();

        $this->gameState->stateChanger->drawCardsForPlayer($player, 1);

        return response()->json(['message' => 'Card drawn']);
    }

    public function concede(Game $game): JsonResponse
    {
        $game->status = 'CONCEDED';
        $game->winner_id = $game->currentPlayer->getOpponent()->id;
        $game->save();

        return response()->json(['message' => 'Game conceded']);
    }
}
