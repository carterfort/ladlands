<?php

namespace Tests\Unit;

use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\People\{AssassinDefinition, LooterDefinition};
use App\Effects\AssassinEffect;
use App\Effects\DestroyEffect;
use App\Models\PlayerInputRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class AssassinTest extends TestCase
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

    public function test_assassin_can_only_target_unprotected_people()
    {
        // Create and place an assassin
        $assassin = $this->testData['game']->cards()
            ->create(['card_definition' => AssassinDefinition::class, 'location' => '{}']);
        $assassinSpace = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($assassin, $assassinSpace);

        // Create a target looter in unprotected position
        $looter = $this->testData['game']->cards()
            ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $looterSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($looter, $looterSpace);

        // Get assassin's destroy ability
        $this->gameState->applyAbilitiesForCardsInPlay();
        $destroyAbility = $this->gameState->getAbilitiesForCard($assassin)[0];

        // Test valid targets - should include unprotected looter
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $destroyAbility,
            $this->testData['playerA']
        )[AssassinEffect::class];
        $this->assertContains($looter->location->space_id, $validTargets->toArray());

        // Create a protected looter
        $protectingLooter = $this->testData['game']->cards()
            ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $protectedSpace = $this->testData['boardB']->battlefield()->wherePosition(4)->first();
        $this->gameState->stateChanger->putCardInSpace($protectingLooter, $protectedSpace);

        // Test valid targets again - should not include protected looter
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $destroyAbility,
            $this->testData['playerA']
        )[AssassinEffect::class];
        $this->assertNotContains($looter->location->space_id, $validTargets->toArray());
    }

    public function test_assassin_destroy_effect_discards_target()
    {
        // Create and place an assassin
        $assassin = $this->testData['game']->cards()
            ->create(['card_definition' => AssassinDefinition::class, 'location' => '{}']);
        $assassinSpace = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($assassin, $assassinSpace);

        // Create a target looter
        $looter = $this->testData['game']->cards()
            ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $looterSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($looter, $looterSpace);

        // Get assassin's destroy ability
        $this->gameState->applyAbilitiesForCardsInPlay();
        $destroyAbility = $this->gameState->getAbilitiesForCard($assassin)[0];

        // Create input request to destroy the looter
        $request = new PlayerInputRequest();
        $request->owningPlayer = $this->testData['playerA'];
        $request->selected_targets = [$looter->location->space_id];
        
        // Apply the destroy effect
        $effect = app('effects')->get($destroyAbility->effectClasses)[0];
        $effect->applyWithInput($this->gameState, $request);

        // Verify the looter is in the discard deck
        $this->assertTrue($this->gameState->getGameCardsQuery()->discardDeck()->get()->contains($looter));
    }

}
