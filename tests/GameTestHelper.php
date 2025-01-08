<?php

namespace Tests;

use App\Models\{Game, Player, GameBoard, GameBoardSpace, Card};

trait GameTestHelper
{
    protected function createGameWithPlayers(): array
    {
        $game = Game::factory()->create();

        $playerA = Player::factory()->create([
            'game_id' => $game->id,
            'water' => 10,
            'game_seat' => 'A'
        ]);

        $playerB = Player::factory()->create([
            'game_id' => $game->id,
            'water' => 10,
            'game_seat' => 'One'
        ]);

        $game->current_player_id = $playerA->id;
        $game->save();

        $boardA = GameBoard::factory()->create([
            'game_id' => $game->id,
            'player_id' => $playerA->id
        ]);

        $boardB = GameBoard::factory()->create([
            'game_id' => $game->id,
            'player_id' => $playerB->id
        ]);

        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                GameBoardSpace::factory()->create([
                    'game_board_id' => $boardA->id,
                    'battlefield_position' => [$col, $row]
                ]);
                GameBoardSpace::factory()->create([
                    'game_board_id' => $boardB->id,
                    'battlefield_position' => [$col, $row]
                ]);
            }
        }

        return [
            'game' => $game,
            'playerA' => $playerA,
            'playerB' => $playerB,
            'boardA' => $boardA,
            'boardB' => $boardB
        ];
    }

    protected function placeCardOnBoard(GameBoard $board, $cardDefinition, array $position): Card
    {
        $space = $board->spaces()->whereJsonContains('battlefield_position', $position)->first();

        $card = Card::factory()->create([
            'game_id' => $board->game_id,
            'card_definition' => $cardDefinition::class,
            'is_damaged' => false,
            'location' => [
                'type' => 'BOARD_SPACE',
                'space_id' => $space->id
            ]
        ]);
        

        return $card;
    }

    protected function isProtected(GameBoardSpace $space): bool
    {
        $card = Card::where('location->type', 'BOARD_SPACE')->where('location->space_id', $space->id)->first();

        if (!$card) {
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

        return $frontSpace && $card && !$card->is_destroyed;
    }
}
