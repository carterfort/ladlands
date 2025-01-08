<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Game;
use App\Models\GameBoardSpace;
use App\Models\Player;

class GameStateChangeService {

    /** I might not need from here --- */
    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /** ... to here. Let's see?  */

    public function damageCard(Card $card){
        // Do all the logic about how to handle damage to a card.
    }

    public function advanceEventInQueue(Card $event){

    }

    public function drawCardsForPlayer(Player $player, int $count){

    }

    public function discardCards(array $cardIds){

    }

    public function putCardInGameboardSpace(Card $card, GameBoardSpace $space){

    }


    
}