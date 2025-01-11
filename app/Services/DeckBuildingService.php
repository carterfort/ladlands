<?php

namespace App\Services;

use App\Cards\Camps\CampDefinition;
use App\Cards\Events\EventDefinition;
use App\Cards\People\PersonDefinition;
use App\Models\Card;
use App\Models\Game;
use Illuminate\Support\Facades\File;

class DeckBuildingService {

    protected Game $game;

    public function buildDecks(Game $game)
    {
        $this->game = $game;

        // Helper function to create cards from directory
        $createCardsFromDirectory = function (string $directory, string $parentClass, string $deckType) {
            $namespace = 'App\\Cards\\' . basename($directory) . '\\';
            $path = app_path('Cards/' . basename($directory));

            // Get all PHP files in the directory
            $files = File::files($path);

            foreach ($files as $file) {
                // Get the class name from the file
                $className = $namespace . $file->getFilenameWithoutExtension();

                // Skip if class doesn't exist or isn't a child of the parent class
                if (!class_exists($className) || !is_subclass_of($className, $parentClass)) {
                    continue;
                }

                // Create new instance of the definition
                $definition = new $className();

                // Create and save the card
                $card = new Card();
                $card->game()->associate($this->game);
                $card->card_definition = get_class($definition);
                $card->location = ['type' => $deckType];
                $card->save();
            }
        };

        // Process camp cards
        $createCardsFromDirectory(
            'Camps',
            CampDefinition::class,
            'camp_deck'
        );

        // Process people cards
        $createCardsFromDirectory(
            'People',
            PersonDefinition::class,
            'punk_deck'
        );

        // Process event cards
        $createCardsFromDirectory(
            'Events',
            EventDefinition::class,
            'punk_deck'
        );
    }
}