<?php

namespace App\Http\Controllers;

use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlayerInputRequestController extends Controller
{
    public function __construct(
        private GameStateService $gameState
    ) {}

    public function update(Request $request, PlayerInputRequest $playerInputRequest): JsonResponse
    {
        $request->validate([
            'selected_targets' => 'required|array',
            'selected_targets.*' => 'exists:game_board_spaces,id'
        ]);

        $this->gameState->setGame($playerInputRequest->game);

        // Update selected targets
        $playerInputRequest->selected_targets = $request->selected_targets;
        $playerInputRequest->status = 'COMPLETED';
        $playerInputRequest->save();

        // Process the request
        $this->gameState->handleInputRequestResponse($playerInputRequest);

        return response()->json(['message' => 'Input processed successfully']);
    }

    public function cancel(PlayerInputRequest $playerInputRequest): JsonResponse
    {
        $playerInputRequest->status = 'CANCELLED';
        $playerInputRequest->save();

        return response()->json(['message' => 'Request cancelled']);
    }

    public function chooseOptionalAction(
        Request $request,
        PlayerInputRequest $playerInputRequest
    ): JsonResponse {
        $request->validate([
            'choice' => 'required|boolean'
        ]);

        if ($request->choice) {
            $playerInputRequest->status = 'PENDING';
        } else {
            $playerInputRequest->status = 'CANCELLED';
        }

        $playerInputRequest->save();

        return response()->json(['message' => 'Choice recorded']);
    }
}
