<?php

namespace Tests\Unit;

use App\Cards\Camps\BloodBankDefinition;
use App\Cards\People\GunnerDefinition;
use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use Tests\GameTestHelper;
use Tests\TestCase;


class BloodBankTest extends TestCase
{
    private array $testData;
    private TargetResolver $resolver;
    private GameStateService $gameState;

    use GameTestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testData = $this->createGameWithPlayers();

        $this->resolver = new TargetResolver();
        $this->gameState = $this->app->make(GameStateService::class);
        $this->gameState->setGame($this->testData['game']);
        $this->gameState->buildDecks();
    }

    public function test_blood_bank_ability_requires_valid_sacrifice_target()
    {
        // Get blood bank from deck and put in play
        $bloodBank = $this->testData['game']->cards()->whereCardDefinition(BloodBankDefinition::class)->first();
        $this->gameState->stateChanger->putCardInSpace($bloodBank, $this->testData['playerA']->board->spaces->first());

        // Initially no abilities available with no valid targets
        $this->gameState->applyAbilitiesForCardsInPlay();
        $this->assertEmpty($this->gameState->getAbilitiesForCard($bloodBank));

        // Add a person to sacrifice
        $gunner = $this->testData['game']->cards()->whereCardDefinition(GunnerDefinition::class)->first();
        $this->gameState->stateChanger->putCardInSpace($gunner, $this->testData['playerA']->board->spaces->last());

        // Now ability should be available
        $this->gameState->applyAbilitiesForCardsInPlay();
        $abilities = $this->gameState->getAbilitiesForCard($bloodBank);

        $this->assertNotEmpty($abilities);
        $this->assertEquals('Blood Donation', $abilities[0]->title);
    }
}
