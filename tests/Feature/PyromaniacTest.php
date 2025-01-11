<?php

namespace Tests\Unit;

use App\Cards\Camps\ResonatorDefinition;
use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\People\PyromaniacDefinition;
use App\Models\PlayerInputRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class PyromaniacTest extends TestCase
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
    }

    public function test_pyromaniac_can_only_target_unprotected_camps()
    {
        // Create and place a pyromaniac
        $pyromaniac = $this->testData['game']->cards()
            ->create(['card_definition' => PyromaniacDefinition::class, 'location' => '{}']);
        $pyromaniacSpace = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($pyromaniac, $pyromaniacSpace);

        // Create a target camp in unprotected position
        $camp = $this->testData['game']->cards()
            ->create(['card_definition' => ResonatorDefinition::class, 'location' => '{}']);
        $campSpace = $this->testData['boardB']->battlefield()->wherePosition(4)->first();
        $this->gameState->stateChanger->putCardInSpace($camp, $campSpace);

        // Get pyromaniac's ignite ability
        $this->gameState->applyAbilitiesForCardsInPlay();
        $igniteAbility = $this->gameState->getAbilitiesForCard($pyromaniac)[0];

        // Test valid targets - should include unprotected camp
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $igniteAbility,
            $this->testData['playerA']
        );
        $this->assertContains($camp->location->space_id, $validTargets->toArray());

        // Create a protected camp
        $protectedCamp = $this->testData['game']->cards()
            ->create(['card_definition' => ResonatorDefinition::class, 'location' => '{}']);
        $protectedSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($protectedCamp, $protectedSpace);

        // Test valid targets again - should not include protected camp
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $igniteAbility,
            $this->testData['playerA']
        );
        $this->assertNotContains($protectedCamp->location->space_id, $validTargets->toArray());
    }

    public function test_pyromaniac_effect_destroys_target_camp()
    {
        // Create and place a pyromaniac
        $pyromaniac = $this->testData['game']->cards()
            ->create(['card_definition' => PyromaniacDefinition::class, 'location' => '{}']);
        $pyromaniacSpace = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($pyromaniac, $pyromaniacSpace);

        // Create a target camp
        $camp = $this->testData['game']->cards()
            ->create(['card_definition' => ResonatorDefinition::class, 'location' => '{}']);
        $campSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($camp, $campSpace);

        // Get pyromaniac's ignite ability
        $this->gameState->applyAbilitiesForCardsInPlay();
        $igniteAbility = $this->gameState->getAbilitiesForCard($pyromaniac)[0];

        // Create input request to destroy the camp
        $request = new PlayerInputRequest();
        $request->owningPlayer = $this->testData['playerA'];
        $request->selected_targets = [$camp->location->space_id];

        // Apply the ignite effect
        $effect = app('effects')->get($igniteAbility->effectClass);
        $effect->applyWithInput($this->gameState, $request);

        // Verify the camp is damaged
        $this->assertTrue($camp->fresh()->is_damaged);
    }
}
