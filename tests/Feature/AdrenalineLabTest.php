<?php

namespace Tests\Feature;

use App\Cards\Camps\AdrenalineLabDefinition;
use App\Cards\People\AssassinDefinition;
use App\Cards\People\PyromaniacDefinition;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class AdrenalineLabTest extends TestCase
{
    use GameTestHelper;
    use RefreshDatabase;

    private array $testData;
    private TargetResolver $resolver;
    private GameStateService $gameState;


    protected function setUp(): void
    {
        parent::setUp();
        $this->testData = $this->createGameWithPlayers();
        $this->resolver = new TargetResolver();
        $this->gameState = $this->app->make(GameStateService::class);
        $this->gameState->setGame($this->testData['game']);
        $this->gameState->buildDecks();
    }

    public function test_adrenaline_lab_copies_and_destroys_target()
    {
        $this->testData['playerA']->water = 5;
        $this->testData['playerA']->save();

        // Set up spaces
        $labSpace = $this->testData['boardA']->battlefield()->wherePosition(1)->first();
        $assassinSpace = $this->testData['boardA']->battlefield()->wherePosition(2)->first();
        $targetSpace = $this->testData['boardB']->battlefield()->wherePosition(0)->first();

        // Get and position cards
        $labCard = $this->gameState->getGameCardsQuery()
            ->where('card_definition', AdrenalineLabDefinition::class)
            ->first();
        $assassinCard = $this->gameState->getGameCardsQuery()
            ->where('card_definition', AssassinDefinition::class)
            ->first();
        $targetCard =
        $this->gameState->getGameCardsQuery()
            ->where('card_definition', PyromaniacDefinition::class)
            ->first();

        $labCard->location = ['type' => 'BATTLEFIELD', 'space_id' => $labSpace->id];
        $labCard->save();

        $assassinCard->location = ['type' => 'BATTLEFIELD', 'space_id' => $assassinSpace->id];
        $assassinCard->is_damaged = true;
        $assassinCard->save();

        $targetCard->location = ['type' => 'BATTLEFIELD', 'space_id' => $targetSpace->id];
        $targetCard->save();

        // First request to select damaged assassin
        $request1 = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerA']->id,
            'source_card_id' => $labCard->id,
            'selected_targets' => [$assassinCard->location->space_id],
            'valid_targets' => [],
            'effect_key' => 'App\\Effects\\AdrenalineLabEffect'
        ]);

        // Second request for the copied assassin ability
        $request2 = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerA']->id,
            'source_card_id' => $assassinCard->id,
            'selected_targets' => [$targetCard->location->space_id],
            'valid_targets'=> [$targetCard->location->space_id],
            'effect_key' => 'App\\Effects\\AssassinEffect'
        ]);

        // Apply effects
        $this->gameState->handleInputRequestResponse($request1);
        $this->gameState->handleInputRequestResponse($request2);

        // Verify results
        $assassinCard->refresh();
        $targetCard->refresh();
        $this->assertEquals('discard_deck', $assassinCard->location->type);
        $this->assertEquals('discard_deck', $targetCard->location->type);
    }
}
