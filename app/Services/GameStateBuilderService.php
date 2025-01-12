<?php

namespace App\Services;

use App\Models\{Game, Player, Card};

class GameStateBuilderService
{
    private GameState $state;
    private Game $game;
    private Player $requestingPlayer;

    public function __construct(private GameStateService $gameStateService)
    {
        $this->state = new GameState();
    }

    public function forGame(Game $game): self
    {
        $this->game = $game;
        $this->gameStateService->setGame($game);
        $this->state->gameId = $game->id;
        $this->state->activePlayerId = $game->current_player_id;
        return $this;
    }

    public function forRequestingPlayer(Player $player): self
    {
        $this->requestingPlayer = $player;
        $this->state->player = $this->buildPlayerState($player);
        $this->state->opponent = $this->buildPlayerState($player->getOpponent());
        return $this;
    }

    public function build(): GameState
    {
        $this->buildGameboards()
            ->buildGameCards()
            ->buildPendingRequests()
            ->buildDeckCounts();

        // Update available abilities for cards in play
        $this->gameStateService->applyAbilitiesForCardsInPlay();

        return $this->state;
    }

    private function buildPlayerState(Player $player): array
    {
        return [
            'id' => $player->id,
            'water' => $player->water,
            'hand' => $player->id === $this->requestingPlayer->id
                ? $player->hand()->get()->map(fn($card) => $card->id)
                : $player->hand()->count()
        ];
    }

    private function buildGameboards(): self
    {
        $this->state->gameboards = $this->game->players->map(function ($player) {
            return [
                'player_id' => $player->id,
                'spaces' => $player->board->spaces->map(fn($space) => [
                    'id' => $space->id,
                    'type' => $space->type,
                    'position' => $space->position
                ])
            ];
        });
        return $this;
    }

    private function buildGameCards(): self
    {
        $this->state->gameCards = $this->game->cards()
            ->whereJsonContains('location->type', 'BATTLEFIELD')
            ->get()
            ->mapWithKeys(function ($card) {
                return [$card->location->space_id => $this->formatCard($card)];
            });

        $this->state->availableAbilities = collect($this->gameStateService->getAvailableAbilities())
            ->map(fn($abilities, $cardId) => collect($abilities)->map(fn($ability) => [
                'title' => $ability->title,
                'description' => $ability->description,
                'cost' => $ability->cost,
                'valid_targets' => $this->gameStateService->getValidTargetsForAbility($ability, $this->requestingPlayer)
            ]));

        return $this;
    }

    private function buildPendingRequests(): self
    {
        $this->state->pendingRequest = $this->game->playerInputRequests()
            ->where('player_id', $this->requestingPlayer->id)
            ->pending()
            ->first();

        $this->state->opponentPendingRequest = $this->game->playerInputRequests()
            ->where('player_id', $this->requestingPlayer->getOpponent()->id)
            ->pending()
            ->exists();

        return $this;
    }

    private function buildDeckCounts(): self
    {
        $this->state->deckCounts = [
            'punk_deck' => $this->game->cards()->whereJsonContains('location->type', 'punk_deck')->count(),
            'camp_deck' => $this->game->cards()->whereJsonContains('location->type', 'camp_deck')->count(),
            'discard_deck' => $this->game->cards()->whereJsonContains('location->type', 'discard_deck')->count()
        ];
        return $this;
    }

    private function formatCard(Card $card): array
    {
        $definition = $card->getDefinition();
        return [
            'id' => $card->id,
            'title' => $definition->title,
            'description' => $definition->description,
            'type' => $definition->type,
            'is_damaged' => $card->is_damaged,
            'is_ready' => $card->is_ready
        ];
    }
}
