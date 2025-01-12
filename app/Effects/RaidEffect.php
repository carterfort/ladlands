<?php

namespace App\Effects;

use App\Models\Player;
use App\Services\GameStateService;

class RaidEffect implements ApplyToPlayerImmediatelyEffect
{
    public function __construct(
        public readonly string $title = "Raid",
        public readonly string $description = "Play or advance Raiders event",
    ) {}

    public function apply(GameStateService $state, Player $player): void
    {
        // Try to find Raiders in the event queue
        $raiders = $state->getGameCardsQuery()
            ->where('card_definition', 'App\\Cards\\Events\\RaidersDefinition')
            ->whereJsonContains('location->type', 'EVENT_QUEUE')
            ->where('location->player_id', $player->id)
            ->first();

        if ($raiders) {
            // Advance Raiders by one position
            $currentPosition = $raiders->location->position;
            $raiders->location = [
                'type' => 'EVENT_QUEUE',
                'player_id' => $player->id,
                'position' => max(0, $currentPosition - 1)
            ];
            $raiders->save();
        } else {
            // Try to find Raiders in the deck to play
            $raiders = $state->getGameCardsQuery()
                ->where('card_definition', 'App\\Cards\\Events\\RaidersDefinition')
                ->whereJsonContains('location->type', 'punk_deck')
                ->first();

            if ($raiders) {
                $raiders->location = [
                    'type' => 'EVENT_QUEUE',
                    'player_id' => $player->id,
                    'position' => 2
                ];
                $raiders->save();
            }
        }
    }

    public function getTargetingRequirements(): array
    {
        return [];
    }
}