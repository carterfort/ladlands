<?php

namespace Tests\Unit;

use App\Models\{Card, Game, Player, GameBoard, GameBoardSpace};
use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\People\{LooterDefinition, GuardDefinition};
use App\Abilities\{DamageAbility, ProtectAbility};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_looter_targeting_guard()
    {
        // Place Looter in back row of Player A's board
        $looter = $this->placeCardOnBoard(
            $this->testData['boardA'],
            new LooterDefinition(),
            [0, 2] // Back row, left column
        );

        // Place Guard in front row of Player B's board with nothing in front 
        $guard = $this->placeCardOnBoard(
            $this->testData['boardB'],
            new GuardDefinition(),
            [0, 0] // Front row, left column
        );

        // Test Looter's damage ability targeting
        $damageAbility = new DamageAbility(2);
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $damageAbility, 
            $this->testData['playerA']
        );

        // Guard should be targetable as it's in front row
        $this->assertContains($guard->location->space_id, $validTargets->toArray());

        // Place a protecting card in front of Guard
        $protector = $this->placeCardOnBoard(
            $this->testData['boardB'], 
            new GuardDefinition(), 
            [0,1]  // Position 3 is middle row, same column
        );

        DB::enableQueryLog();

        // Check targets again - Guard should no longer be targetable
        $validTargets = $this->gameState->getValidTargetsForAbility(
            $damageAbility,
            $this->testData['playerA']
        );
        $cardsWithLocations = Card::whereNotNull('location->space_id')->pluck('location')->map->space_id;
        dd(GameBoardSpace::whereIn('id', $cardsWithLocations->toArray())->pluck('battlefield_position'));

        $this->assertNotContains($guard->location->space_id, $validTargets->toArray());
    }
}

// Card definitions remain the same as before