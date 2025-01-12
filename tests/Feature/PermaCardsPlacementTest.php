<?php

namespace Tests\Unit;

use App\Services\GameStateService;
use App\Targeting\TargetResolver;
use App\Cards\Perma\RaidersDefinition;
use App\Cards\Perma\WaterSiloDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\GameTestHelper;

class PermaCardsPlacementTest extends TestCase
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

    public function test_perma_cards_are_placed_correctly()
    {
        // Place perma cards
        $this->gameState->placePermaCards();

        // For each player
        foreach ($this->testData['game']->players as $player) {
            // Get their perma spaces
            $permaSpaces = $player->board->spaces()->where('type', 'PERMA')->pluck('id');

            // Check they have exactly two perma cards
            $permaCards = $this->testData['game']->cards()
                ->whereIn('location->space_id', $permaSpaces)
                ->get();

            $this->assertCount(2, $permaCards, "Player should have exactly 2 perma cards");

            // Verify one is Water Silo and one is Raiders
            $cardDefinitions = $permaCards->pluck('card_definition')->toArray();
            $this->assertContains(WaterSiloDefinition::class, $cardDefinitions, "Player should have a Water Silo");
            $this->assertContains(RaidersDefinition::class, $cardDefinitions, "Player should have Raiders");

            // Verify they're in the correct positions
            foreach ($permaCards as $card) {
                $spacePosition = $player->board->spaces()
                    ->where('id', $card->location->space_id)
                    ->value('position');

                if ($card->card_definition === WaterSiloDefinition::class) {
                    $this->assertEquals(1, $spacePosition, "Water Silo should be in position 1");
                } else {
                    $this->assertEquals(2, $spacePosition, "Raiders should be in position 2");
                }
            }
        }
    }
}
