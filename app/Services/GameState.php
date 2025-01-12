<?php

namespace App\Services;

class GameState
{
    public $gameId;
    public $isReady;
    public $activePlayerId;
    public $player;
    public $opponent;
    public $gameboards;
    public $gameCards;
    public $availableAbilities;
    public $pendingRequest;
    public $opponentPendingRequest;
    public $turnOccuranceLog;
    public $deckCounts;
    public $validPersonTargetSpaces;
    public $winningPlayer = null;

    public function toArray()
    {
        $gameId = $this->gameId;
        $isReady = $this->isReady;
        $activePlayerId = $this->activePlayerId;
        $player = $this->player;
        $opponent = $this->opponent;
        $gameboards = $this->gameboards;
        $gameCards = $this->gameCards;
        $availableAbilities = $this->availableAbilities;
        $pendingRequest = $this->pendingRequest;
        $turnOccuranceLog = $this->turnOccuranceLog;
        $opponentPendingRequest = $this->opponentPendingRequest;
        $validPersonTargetSpaces = $this->validPersonTargetSpaces;
        $deckCounts = $this->deckCounts;

        $winningPlayer = $this->winningPlayer;
        return compact('gameId', 'isReady', 'deckCounts', 'activePlayerId', 'player', 'opponent', 'gameboards', 'gameCards', 'pendingRequest', 'availableAbilities', 'opponentPendingRequest', 'validPersonTargetSpaces', 'turnOccuranceLog', 'winningPlayer');
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}