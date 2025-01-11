<?php

namespace Tests\Unit;

use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\Camps\{ResonatorDefinition};
use App\Cards\People\{LooterDefinition};
use App\Abilities\{DamageAbility};
use App\Effects\DamageEffect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class TargetResolverTest extends TestCase
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

    public function test_a_card_protects_another_card()
    {
        $looter = $this->testData['game']->cards()
            ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $looterSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($looter, $looterSpace);

        // Test Looter's damage ability targeting
        $damageAbility = new DamageAbility(2);
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $damageAbility, 
            $this->testData['playerA']
        )[DamageEffect::class];

        // Guard should be targetable as it's in front row
        $this->assertContains($looter->location->space_id, $validTargets->toArray());

        $looter2 = $this->testData['game']->cards()
            ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $looter2Space = $this->testData['boardB']->battlefield()->wherePosition(4)->first();
        $this->gameState->stateChanger->putCardInSpace($looter2, $looter2Space);

        $validTargets = $this->gameState->getValidTargetsForAbility(
            $damageAbility,
            $this->testData['playerA']
        )[DamageEffect::class];

        $this->assertNotContains($looter->location->space_id, $validTargets->toArray());
    }

    public function test_damaged_unprotected_cards_only(){

        $looter = $this->testData['game']->cards()
                    ->create(['card_definition' => LooterDefinition::class, 'location' => '{}']);
        $looterSpace = $this->testData['boardB']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($looter, $looterSpace);


        $resonator = $this->testData['game']->cards()
                    ->create(['card_definition' => ResonatorDefinition::class, 'location' => '{}']);
        $resonatorSpace = $this->testData['boardA']->battlefield()->wherePosition(7)->first();
        $this->gameState->stateChanger->putCardInSpace($resonator, $resonatorSpace);

        $this->gameState->applyAbilitiesForCardsInPlay();

        $resonatorAbility = $this->gameState->getAbilitiesForCard($resonator)[0];
        // There should be no valid targets because the Looter isn't damaged

        $validTargets = $this->gameState->getValidTargetsForAbility(
            $resonatorAbility,
            $this->testData['playerA']
        )["App\Effects\ResonatorEffect"];

        $this->assertCount(0, $validTargets);

        $this->gameState->stateChanger->damageCard($looter);

        // There should be a valid target, and it should be the space with the looter in it
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $resonatorAbility,
            $this->testData['playerA']
        )["App\Effects\ResonatorEffect"];

        $this->assertCount(1, $validTargets);
        

    }
}