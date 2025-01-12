<?php

namespace App\Services;

use App\Models\Card;
use App\Models\GameBoardSpace;
use App\Models\Player;

class EventHandlerService {

    public function advanceEvent(Card $event){
        // Get this card's position
        $position = GameBoardSpace::findOrFail($event->location->space_id)->position;
        if ($position == 1){
            
        }
    }

}