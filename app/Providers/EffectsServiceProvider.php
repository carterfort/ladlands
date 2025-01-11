<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use App\Effects\Effect;

class EffectsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the effects registry as a singleton
        $this->app->singleton('effects', function ($app) {
            return new class {
                private $effects = [];

                public function register($key, $effect): void
                {
                    $this->effects[$key] = $effect;
                }

                public function get($keys)
                {
                    if (!is_array($keys)){
                        $keys = [$keys];
                    }
                    $effects = [];
                    foreach($keys as $key){
                        $effects[] = $this->effects[$key] ?? null;
                    }
                    return $effects;
                }

                public function all()
                {
                    return $this->effects;
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $effectsPath = app_path('Effects');

        // Ensure the Effects directory exists
        if (!File::isDirectory($effectsPath)) {
            return;
        }

        // Get all PHP files in the Effects directory
        $files = File::allFiles($effectsPath);

        foreach ($files as $file) {
            $className = 'App\\Effects\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Check if the class exists and extends Effect
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->isSubclassOf(Effect::class) && !$reflection->isAbstract()) {
                    // Register the effect using its full class name as the key
                    $effect = new $className();
                    $this->app['effects']->register($className, $effect);
                }
            }
        }
    }
}
