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
        $card->is_damaged = true;
        $card->save();
    }

    public function destroyCard(Card $card){
        // Do all the logic about flipping camps or sending the card to the appropriate deck
        if ($card->is_punk){
            $card->location = ['type' => 'deck'];
        } elseif($card->type == 'camp'){
            $card->is_flipped = true;
        } else {
            $card->location = ['type' => 'discard'];
        }
        $card->save();
    }

    public function advanceEventInQueue(Card $event){

    }

    public function drawCardsForPlayer(Player $player, int $count){
        $cards = $this->game->punkDeck->take($count);
        $location = ['type' => 'player_hand', 'player_id' => $player->id];
        $cards->update(['location' => $location]);
    }

    public function discardCards(array $cardIds){

    }

    public function putCardInSpace(Card $card, GameBoardSpace $space)
    {
        $card->location = [
            'space_id' => $space->id,
            'type' => 'BATTLEFIELD'
        ];
        $card->save();
    }


    public function addWaterForPlayer(Player $player, $amount)
    {
        $player->water += $amount;
        $player->save();
    }


    
}