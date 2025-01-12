<?php

namespace Tests\Feature;

use App\Cards\People\AssassinDefinition;
use App\Models\Card;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Services\GameStateBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class GameStateTest extends TestCase
{
    use RefreshDatabase;
    use GameTestHelper;

    private GameStateService $gameState;
    private GameStateBuilderService $gameStateBuilder;
    private array $testData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testData = $this->createGameWithPlayers();
        $this->gameState = $this->app->make(GameStateService::class);
        $this->gameStateBuilder = $this->app->make(GameStateBuilderService::class);
        $this->gameState->setGame($this->testData['game']);
        $this->gameState->buildDecks();
    }

    public function test_game_state_has_correct_structure()
    {
        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $response->assertOk()
            ->assertJsonStructure([
                'gameId',
                'isReady',
                'deckCounts' => [
                    'punk_deck',
                    'camp_deck',
                    'discard_deck'
                ],
                'activePlayerId',
                'player' => [
                    'id',
                    'water',
                    'hand'
                ],
                'opponent' => [
                    'id',
                    'water',
                    'hand'
                ],
                'gameboards' => [
                    '*' => [
                        'player_id',
                        'spaces' => [
                            '*' => [
                                'id',
                                'type',
                                'position'
                            ]
                        ]
                    ]
                ],
                'gameCards',
                'pendingRequest',
                'availableAbilities',
                'opponentPendingRequest',
                'validPersonTargetSpaces',
                'turnOccuranceLog',
                'winningPlayer'
            ]);
    }

    public function test_game_state_has_correct_player_info()
    {
        $this->testData['playerA']->water = 5;
        $this->testData['playerB']->water = 3;
        $this->testData['playerA']->save();
        $this->testData['playerB']->save();

        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $response->assertOk()
            ->assertJson([
                'player' => [
                    'id' => $this->testData['playerA']->id,
                    'water' => 5,
                    'hand' => []
                ],
                'opponent' => [
                    'id' => $this->testData['playerB']->id,
                    'water' => 3,
                    'hand' => 0
                ]
            ]);
    }

    public function test_cards_in_play_appear_in_game_cards()
    {
        $assassin = Card::factory()->create([
            'game_id' => $this->testData['game']->id,
            'card_definition' => AssassinDefinition::class
        ]);

        $space = $this->testData['boardA']->battlefield()->first();
        $this->gameState->stateChanger->putCardInSpace($assassin, $space);

        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $data = $response->json();

        $this->assertArrayHasKey($space->id, $data['gameCards']);
        $this->assertEquals('Assassin', $data['gameCards'][$space->id]['title']);
    }

    public function test_pending_requests_are_included()
    {
        $request = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerA']->id,
            'effect_key' => 'App\\Effects\\DamageEffect',
            'valid_targets' => [],
            'selected_targets' => []
        ]);

        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $data = $response->json();
        $this->assertNotNull($data['pendingRequest']);
        $this->assertEquals($request->id, $data['pendingRequest']['id']);
    }

    public function test_opponent_pending_request_is_boolean()
    {
        $request = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerB']->id,
            'effect_key' => 'App\\Effects\\DamageEffect',
            'valid_targets' => [],
            'selected_targets' => []
        ]);

        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $data = $response->json();
        $this->assertTrue($data['opponentPendingRequest']);
    }

    public function test_deck_counts_are_accurate()
    {
        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $data = $response->json();
        $this->assertIsArray($data['deckCounts']);
        $this->assertArrayHasKey('punk_deck', $data['deckCounts']);
        $this->assertArrayHasKey('camp_deck', $data['deckCounts']);
        $this->assertArrayHasKey('discard_deck', $data['deckCounts']);
        $this->assertIsInt($data['deckCounts']['punk_deck']);
        $this->assertIsInt($data['deckCounts']['camp_deck']);
        $this->assertIsInt($data['deckCounts']['discard_deck']);
    }

    public function test_gameboard_spaces_are_correctly_structured()
    {
        $response = $this->actingAs($this->testData['playerA']->user)
            ->getJson("/api/game/{$this->testData['game']->id}/state");

        $data = $response->json();
        $playerBoard = collect($data['gameboards'])->firstWhere('player_id', $this->testData['playerA']->id);

        $this->assertCount(14, $playerBoard['spaces']); // 9 battlefield + 3 event + 2 perma
        $this->assertEquals(9, collect($playerBoard['spaces'])->where('type', 'BATTLEFIELD')->count());
        $this->assertEquals(3, collect($playerBoard['spaces'])->where('type', 'EVENT')->count());
        $this->assertEquals(2, collect($playerBoard['spaces'])->where('type', 'PERMA')->count());
    }
}
