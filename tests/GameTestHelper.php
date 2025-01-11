<?php

namespace Tests;

use App\Models\{Game, Player, GameBoard, GameBoardSpace, Card, CardContainer};

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

        collect([$boardA, $boardB])->each(function(GameBoard $board){
            for ($fieldSpace = 0; $fieldSpace < 9; $fieldSpace++) {
                $space = new GameBoardSpace();
                $space->game_board_id = $board->id;
                $space->type = "BATTLEFIELD";
                $space->position = $fieldSpace;
                $space->save();
            }
            for ($i = 0; $i < 3; $i++){
                $space = new GameBoardSpace();
                $space->game_board_id = $board->id;
                $space->type = "EVENT";
                $space->position = $i;
                $space->save();
            }
            for ($i = 0; $i < 2; $i++) {
                $space = new GameBoardSpace();
                $space->game_board_id = $board->id;
                $space->type = "PERMA";
                $space->position = $i;
                $space->save();
            }
        });


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