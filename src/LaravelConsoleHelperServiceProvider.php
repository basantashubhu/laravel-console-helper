<?php

namespace Basanta\LaravelConsoleHelper;

use Illuminate\Support\ServiceProvider;

class LaravelConsoleHelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\MakePHPClass::class,
            Commands\MakeComposerRepository::class,
        ]);
    }
}