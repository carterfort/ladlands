<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GenerateCardDefinition extends Command
{
    protected $signature = 'generate:card {name} {--type=Person}';
    protected $description = 'Create a new game card with its associated ability and effect';

    protected $typeNamespaces = [
        'Person' => 'App\\Cards\\People',
        'Camp' => 'App\\Cards\\Camps',
        'Event' => 'App\\Cards\\Events'
    ];

    protected $baseClasses = [
        'Person' => 'PersonDefinition',
        'Camp' => 'CampDefinition',
        'Event' => 'EventDefinition'
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

        if ($type !== 'Event') {
            $this->createAbility($name);
            $this->createEffect($name);
        } else {
            $this->createEffect($name);
        }

        $this->info("Created {$type} card '{$name}' with associated files!");
        return 0;
    }

    protected function createCardDefinition($name, $type)
    {
        $namespace = $this->typeNamespaces[$type];
        $baseClass = $this->baseClasses[$type];
        $className = "{$name}Definition";

        $template = $this->getCardDefinitionTemplate(
            $namespace,
            $className,
            $baseClass,
            $name,
            $type
        );

        $path = app_path("Cards/{$type}s/{$className}.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    protected function createAbility($name)
    {
        $template = $this->getAbilityTemplate($name);
        $path = app_path("Abilities/{$name}Ability.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    protected function createEffect($name)
    {
        $template = $this->getEffectTemplate($name);
        $path = app_path("Effects/{$name}Effect.php");
        $this->ensureDirectoryExists($path);
        File::put($path, $template);
    }

    protected function getCardDefinitionTemplate($namespace, $className, $baseClass, $name, $type)
    {
        $abilityImport = $type !== 'Event' ? "use App\\Abilities\\{$name}Ability;" : '';
        $abilitiesMethod = $type === 'Person' ? $this->getPersonAbilitiesTemplate($name) : ($type === 'Camp' ? $this->getCampAbilitiesTemplate($name) : '');

        return <<<PHP
<?php

namespace {$namespace};

{$abilityImport}

class {$className} extends {$baseClass}
{
    public string \$title = '{$name}';
    public string \$description = 'Description for {$name}';
    public int \$waterCost = 1;

    {$abilitiesMethod}
}
PHP;
    }

    protected function getPersonAbilitiesTemplate($name)
    {
        return <<<PHP
    
    public function getBaseAbilities(): array
    {
        return [new {$name}Ability()];
    }

    public function registerJunkAbility(): void
    {
        \$this->junkAbility = new {$name}Ability();
    }
PHP;
    }

    protected function getCampAbilitiesTemplate($name)
    {
        return <<<PHP
    
    public function getBaseAbilities(): array
    {
        return [new {$name}Ability()];
    }
PHP;
    }

    protected function getAbilityTemplate($name)
    {
        return <<<PHP
<?php

namespace App\Abilities;

use App\Effects\\{$name}Effect;
use App\Targeting\TargetType;

class {$name}Ability extends Ability
{
    public function __construct()
    {
        parent::__construct(
            \$title = "{$name}",
            \$description = "Description for {$name} ability",
            \$cost = 1,
            \$targetRequirements = [TargetType::OPPONENT, TargetType::UNPROTECTED],
            \$effect = {$name}Effect::class
        );
    }
}
PHP;
    }

    protected function getEffectTemplate($name)
    {
        return <<<PHP
<?php

namespace App\Effects;

use App\Services\GameStateService;

class {$name}Effect extends Effect
{
    public function __construct()
    {
        parent::__construct(
            \$title = "{$name}",
            \$description = "Description for {$name} effect",
        );
    }

    public function applyToGameState(GameStateService \$state, \$card)
    {
        // Implement the effect logic here
        // \$state->stateChanger->someAction(\$card);
    }
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
