<?php

use App\Http\Controllers\GameCardAbilityController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PerformanceRecordController;
use App\Http\Controllers\PlayerInputRequestController;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/games', [GameController::class, 'index']);
    Route::post("/game", [GameController::class, 'store']);
    Route::get('/game/{game}/concede', [GameController::class, 'concede']);
    Route::get("/game/{game}/end-current-turn", [GameController::class, 'endTurn']);
    Route::get("/game/{game}/pay-to-draw", [GameController::class, 'payToDraw']);
    Route::post("/game/{game}/play-person-at-camp-slot", [GameController::class, 'playPersonAtCampGameboardSpace']);
    Route::post("/game/{game}/add-event-to-queue", [GameController::class, 'addEventToQueue']);
    Route::post('/game/{game}/{gameCard}/trigger-ability', [GameCardAbilityController::class, 'trigger']);
    Route::post('/player-input-request/{playerInputRequest}', [PlayerInputRequestController::class, 'update']);
    Route::get('/player-input-request/{playerInputRequest}/cancel', [PlayerInputRequestController::class, 'cancel']);
    Route::post('/player-input-request/{playerInputRequest}/choose-option', [PlayerInputRequestController::class, 'chooseOptionalAction']);
    Route::get('/game/{game}/state', [GameController::class, 'getState']);
});