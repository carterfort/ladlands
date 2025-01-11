<?php

namespace Tests\Unit;

use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\Camps\{ResonatorDefinition};
use App\Cards\People\MuseDefinition;
use App\Effects\LootEffect;
use App\Models\PlayerInputRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class EffectsTest extends TestCase
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
    /**
     * A basic feature test example.
     */
    public function test_the_loot_effect_draws_a_card(): void
    {
        // Create a camp and add it to BoardB
        $resonator = $this->testData['game']->cards()->whereCardDefinition(ResonatorDefinition::class)->first();
        $slot = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($resonator, $slot);

        $request = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerA']->id,
            'valid_targets' => [$slot->id],
            'selected_targets' => [$slot->id],
            'effect_key' => LootEffect::class
        ]);

        $handCountBefore = $this->testData['playerA']->hand->count();

        $this->gameState->handleInputRequestResponse($request);
        
        $handCountAfter = $this->testData['playerA']->hand()->get()->count();

        $this->assertTrue($resonator->fresh()->is_damaged);
        $this->assertEquals($handCountBefore + 1 , $handCountAfter);

    }

    function test_it_adds_water_when_the_muse_is_activated() {
        $muse = $this->testData['game']->cards()->whereCardDefinition(MuseDefinition::class)->first();
        $slot = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($muse, $slot);
        $abilities = $muse->getDefinition()->getBaseAbilities();

        $waterBefore = $this->testData['playerA']->water;

        $this->gameState->playerActivatesAbilityViaCard($this->testData['playerA'], $abilities[0], $muse);

        $waterAfter = $this->testData['playerA']->fresh()->water;

        $this->assertEquals($waterBefore + 1, $waterAfter);
    }
}
