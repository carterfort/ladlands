<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Card;
use App\Services\GameStateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameCardAbilityController extends Controller
{
    public function __construct(
        private GameStateService $gameState
    ) {}

    public function trigger(Request $request, Game $game, Card $gameCard): JsonResponse
    {
        $this->gameState->setGame($game);

        // Validate the ability exists and can be triggered
        $this->gameState->applyAbilitiesForCardsInPlay();
        $abilities = $this->gameState->getAbilitiesForCard($gameCard);

        if (empty($abilities)) {
            return response()->json(['error' => 'No valid abilities available'], 400);
        }

        $ability = $abilities[0]; // Get first available ability

        // Check water cost
        if ($game->currentPlayer->water < $ability->cost) {
            return response()->json(['error' => 'Not enough water'], 400);
        }

        // Pay the cost
        $game->currentPlayer->water -= $ability->cost;
        $game->currentPlayer->save();

        // Trigger the ability
        $this->gameState->playerActivatesAbilityViaCard(
            $game->currentPlayer,
            $ability,
            $gameCard
        );

        return response()->json(['message' => 'Ability triggered successfully']);
    }
}
