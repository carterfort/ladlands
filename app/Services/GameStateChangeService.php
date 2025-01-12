<?php

namespace App\Services;

use App\Effects\ApplyToPlayerImmediatelyEffect;
use App\Models\Card;
use App\Models\GameBoardSpace;
use App\Models\Player;
use App\Models\PlayerInputRequest;

class GameStateChangeService {

    public function endTurn(GameStateService $state){
        $state->getGameCardsQuery()->update(['is_ready' => true]);

    }

    public function startTurnForPlayer(GameStateService $state, Player $player){
        $state->game->update(['current_player_id' => $player->id]);
        $player->update(['water' => 3]);
    }

    public function damageCard(Card $card){
        // Do all the logic about how to handle damage to a card.
        $card->is_damaged = true;
        $card->save();
    }

    public function destroyCard(Card $card){
        // Do all the logic about flipping camps or sending the card to the appropriate deck
        if ($card->is_punk){
            $card->location = ['type' => 'punk_deck'];
            $card->is_punk = false;
            $card->is_damaged = false;
        } elseif($card->type == 'Camp'){
            $card->is_destroyed = true;
        } else {
            $card->location = ['type' => 'discard_deck'];
            $card->is_punk = false;
            $card->is_damaged = false;
        }
        $card->save();
    }

    public function restoreCard(Card $card){
        $card->is_damaged = false;
        $card->save();
    }


    public function drawCardsForPlayer(Player $player, int $count){
        $cards = $player->game->cards()->punkDeck()->take($count);
        $location = ['type' => 'player_hand', 'player_id' => $player->id];
        $cards->update(['location' => $location]);
    }

    public function discardCards(array $cardIds){

    }

    public function readyCard(Card $card){
        $card->is_ready = true;
        $card->save();
    }

    public function putCardInSpace(Card $card, GameBoardSpace $space)
    {
        $card->location = [
            'space_id' => $space->id,
            'type' => 'BATTLEFIELD'
        ];
        $card->save();
    }

    public function putCardInHandForPlayer(Card $card, Player $player){
        $card->location = [
            'type' => 'player_hand',
            'player_id' => $player->id
        ];
        $card->save();
    }

    public function putPunkInSpace(GameStateService $state, GameBoardSpace $space)
    {
        // TODO: Add a check that this card is an event or a person

        // TODO: Check if this needs to trigger a shuffle of Discard => Punk
        $card = $state->getGameCardsQuery()->punk()->shuffle()->first();

        $card->location = [
            'space_id' => $space->id,
            'type' => 'BATTLEFIELD'
        ];
        $card->is_punk = true;
        $card->save();
    }


    public function addWaterForPlayer(Player $player, $amount)
    {
        $player->water += $amount;
        $player->save();
    }


    
}