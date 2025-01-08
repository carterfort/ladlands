<?php

namespace App\Services;

use App\Models\{Game, Card, Player, GameBoardSpace};

class CardLocationManager
{
    public function moveToSpace(Card $card, GameBoardSpace $space): void
    {
        $card->update([
            'location' => json_encode([
                'type' => 'BOARD_SPACE',
                'space_id' => $space->id
            ])
        ]);
    }

    public function moveToHand(Card $card, Player $player): void
    {
        $card->update([
            'location' => json_encode([
                'type' => 'PLAYER_HAND',
                'player_id' => $player->id
            ])
        ]);
    }

    public function moveToDiscard(Card $card): void
    {
        $card->update([
            'location' => json_encode([
                'type' => 'DISCARD_DECK'
            ])
        ]);
    }

    public function getCardsInSpace(GameBoardSpace $space): ?Card
    {
        return Card::where('game_id', $space->board->game_id)
            ->whereJsonContains('location->type', 'BOARD_SPACE')
            ->whereJsonContains('location->space_id', $space->id)
            ->first();
    }

    public function getCardsInHand(Player $player): array
    {
        return Card::where('game_id', $player->game_id)
            ->whereJsonContains('location->type', 'PLAYER_HAND')
            ->whereJsonContains('location->player_id', $player->id)
            ->get()
            ->toArray();
    }

    public function isProtected(GameBoardSpace $space): bool
    {
        $cardInSpace = $this->getCardsInSpace($space);
        if (!$cardInSpace) {
            return false;
        }

        $row = floor($space->position / 3);
        if ($row === 0) {
            return false;
        }

        $col = $space->position % 3;
        $frontSpace = $space->board->spaces()
            ->where('position', ($row - 1) * 3 + $col)
            ->first();

        $protectingCard = $this->getCardsInSpace($frontSpace);
        return $protectingCard && $protectingCard->damage < 2;
    }

    
}
