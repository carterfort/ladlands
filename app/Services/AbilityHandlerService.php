<?php

namespace App\Services;

use App\Abilities\Ability;
use App\Effects\ApplyToPlayerImmediatelyEffect;
use App\Effects\Effect;
use App\Effects\InputDependentEffect;
use App\Models\Player;

class AbilityHandlerService{

    public function activateAbilityByPlayer(GameStateService $gameState, Ability $ability, Player $player){
        
        $effects = app('effects')->get($ability->effectClasses);

        foreach ($effects as $effect){
            $interfaces = collect(class_implements($effect));

            when(
                $interfaces->contains(ApplyToPlayerImmediatelyEffect::class),
                fn() => $effect->apply($gameState, $player),
                $interfaces->contains(InputDependentEffect::class),
                fn() => $this->createPlayerInputRequestForEffect($gameState, $player, $effect)
            );
        }


    }

    protected function createPlayerInputRequestForEffect(GameStateService $gameState, Player $player, Effect $effect){
        // do this
    }
}