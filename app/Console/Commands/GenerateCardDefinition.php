<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GenerateCardDefinition extends Command
{
    protected $signature = 'generate:card {name} {--type=Person}';
    protected $description = 'Create a new game card with its ability and effect files';

    protected $typeNamespaces = [
        'Person' => 'App\\Cards\\People',
        'Camp' => 'App\\Cards\\Camps',
        'Event' => 'App\\Cards\\Events',
        'Perma' => 'App\\Cards\\Perma'
    ];

    protected $baseClasses = [
        'Person' => 'PersonDefinition',
        'Camp' => 'CampDefinition',
        'Event' => 'EventDefinition',
        'Perma' => 'PermaDefinition'
    ];

    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->option('type');

        if (!array_key_exists($type, $this->typeNamespaces)) {
            $this->error("Invalid card type. Must be one of: " . implode(', ', array_keys($this->typeNamespaces)));
            return 1;
        }

        $this->createCardDefinition($name, $type);
        $this->createAbility($name, $type);
        $this->createEffect($name, $type);

        $this->info("Created {$type} card '{$name}' with associated files!");
        return 0;
    }

    protected function createCardDefinition($name, $type)
    {
        $namespace = $this->typeNamespaces[$type];
        $baseClass = $this->baseClasses[$type];
        $className = "{$name}Definition";

        $imports = $this->getImports($type, $name);
        $abilityImplementation = $this->getAbilityImplementation($type, $name);

        $template = <<<PHP
<?php

namespace {$namespace};

{$imports}

class {$className} extends {$baseClass}
{
    public string \$title = '{$name}';
    public string \$description = 'Description for {$name}';
    public int \$waterCost = 1;
{$abilityImplementation}
}
PHP;

        $path = app_path("Cards/" . Str::plural($type) . "/{$className}.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    protected function createAbility($name, $type)
    {
        if ($type === 'Event') {
            return;
        }

        $template = <<<PHP
<?php

namespace App\\Abilities\\Definitions;

use App\\Abilities\\Ability;
use App\\Effects\\{$name}Effect;

class {$name}Ability extends Ability
{
    public function __construct()
    {
        parent::__construct(
            \$title = "{$name}",
            \$description = "Description for {$name}",
            \$cost = 1,
            \$effectClasses = [{$name}Effect::class]
        );
    }
}
PHP;

        $path = app_path("Abilities/Definitions/{$name}Ability.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    protected function createEffect($name, $type)
    {
        $interface = $this->getEffectInterface($type);
        $implementation = $this->getEffectImplementation($type);

        $template = <<<PHP
<?php

namespace App\\Effects;

use App\\Models\\PlayerInputRequest;
use App\\Services\\GameStateService;
use App\\Targeting\\TargetType;

class {$name}Effect implements {$interface}
{
    public function __construct(
        public readonly string \$title = "{$name}",
        public readonly string \$description = "Description for {$name}",
    ){}
{$implementation}
}
PHP;

        $path = app_path("Effects/{$name}Effect.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    private function getImports($type, $name)
    {
        $imports = [];

        if ($type === 'Person') {
            $imports[] = "use App\\Abilities\\BaseAbility;";
            $imports[] = "use App\\Abilities\\Definitions\\{$name}Ability;";
            $imports[] = "use App\\Effects\\Effect;";
            $imports[] = "use App\\Effects\\RestoreEffect;";
        } elseif ($type === 'Camp') {
            $imports[] = "use App\\Abilities\\BaseAbility;";
            $imports[] = "use App\\Abilities\\Definitions\\{$name}Ability;";
        } elseif ($type === 'Event') {
            $imports[] = "use App\\Effects\\Effect;";
            $imports[] = "use App\\Effects\\{$name}Effect;";
            $imports[] = "use App\\Effects\\RaidEffect;";
        }

        return implode("\n", $imports);
    }

    private function getAbilityImplementation($type, $name)
    {
        if ($type === 'Person') {
            return <<<PHP

    public function getBaseAbilities(): array
    {
        return [
            new BaseAbility(new {$name}Ability())
        ];
    }

    public function getJunkEffect(): Effect
    {
        return new RestoreEffect();
    }
PHP;
        } elseif ($type === 'Camp') {
            return <<<PHP

    public function getBaseAbilities(): array
    {
        return [new BaseAbility(new {$name}Ability())];
    }
PHP;
        } elseif ($type === 'Event') {
            return <<<PHP

    public function getEventEffects(): array
    {
        return [new {$name}Effect()];
    }

    public function getJunkEffect(): Effect
    {
        return new RaidEffect();
    }
PHP;
        }

        return '';
    }

    private function getEffectInterface($type)
    {
        return match ($type) {
            'Person', 'Camp' => 'InputDependentEffect',
            'Event' => 'InputDependentEffect, CreatesRequestForOpponent',
            default => 'Effect'
        };
    }

    private function getEffectImplementation($type)
    {
        if ($type === 'Event') {
            return <<<'PHP'

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // Implement event effect logic here
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::YOU,
            TargetType::BATTLEFIELD
        ];
    }
PHP;
        }

        return <<<'PHP'

    public function applyWithInput(GameStateService $state, PlayerInputRequest $request): void
    {
        $targetCard = $state->getGameCardsQuery()
            ->where('location->space_id', $request->selected_targets[0])
            ->firstOrFail();

        // Implement effect logic here
    }

    public function getTargetingRequirements(): array
    {
        return [
            TargetType::OPPONENT,
            TargetType::UNPROTECTED,
            TargetType::BATTLEFIELD
        ];
    }
PHP;
    }

    protected function ensureDirectoryExists($path)
    {
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }
}
