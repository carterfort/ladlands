<?php

namespace Tests\Unit;

use App\Cards\Camps\BloodBankDefinition;
use App\Cards\Camps\CannonDefinition;
use App\Cards\People\GunnerDefinition;
use App\Effects\CannonEffect;
use App\Models\PlayerInputRequest;
use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\GameTestHelper;
use Tests\TestCase;


class CannonTest extends TestCase
{
    private array $testData;
    private TargetResolver $resolver;
    private GameStateService $gameState;

    use GameTestHelper;
    use RefreshDatabase;

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
        $cannon = $this->testData['game']->cards()->whereCardDefinition(CannonDefinition::class)->first();
        $this->gameState->stateChanger->putCardInSpace($cannon, $this->testData['playerA']->board->spaces->first());

        $gunner = $this->testData['game']->cards()->whereCardDefinition(GunnerDefinition::class)->first();
        $this->gameState->stateChanger->putCardInSpace($gunner, $this->testData['playerB']->board->spaces->last());

        // Create and handle input request
        $request = PlayerInputRequest::factory()->create([
            'game_id' => $this->testData['game']->id,
            'player_id' => $this->testData['playerA']->id,
            'valid_targets' =>  [$gunner->location->space_id],
            'selected_targets' =>  [$gunner->location->space_id],
            'source_card_id' => $cannon->id,
            'effect_key' => CannonEffect::class
        ]);

        $this->gameState->handleInputRequestResponse($request);

        $this->assertTrue($cannon->fresh()->is_damaged);
        $this->assertTrue($gunner->fresh()->is_damaged);
    }
}
