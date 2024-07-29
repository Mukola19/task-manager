<?php

namespace App\Providers;

use Knuckles\Scribe\Commands\DiffConfig;
use Knuckles\Scribe\Commands\GenerateDocumentation;
use Knuckles\Scribe\Commands\MakeStrategy;
use Knuckles\Scribe\Commands\Upgrade;
use Knuckles\Scribe\ScribeServiceProvider as ServiceProvider;

class ScribeServiceProvider extends ServiceProvider
{

    protected function registerCommands(): void
    {
        $this->commands([
            GenerateDocumentation::class,
            MakeStrategy::class,
            Upgrade::class,
            DiffConfig::class,
        ]);
    }
}