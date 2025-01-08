<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GameStateService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function show(
        Request $request,         // Laravel auto-injects the current request
        GameStateService $service, // Laravel resolves this from the container
        Game $game           // Route parameter, not type-hinted as Game yet
    ) {

        // Get authenticated user from request
        $player = $game->players()->whereUserId($request->user())->findOrFail();

        $service->setGame($game);

        return response()->json(
            $service->getStateForPlayer($player)
        );
    }
}