<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Basanta\LaravelConsoleHelper\App\Pipeline\CreateModel;
use Basanta\LaravelConsoleHelper\App\Pipeline\CreateRepository;
use Illuminate\Support\Facades\Pipeline;

class MakeNewRepoCommand extends \Illuminate\Console\Command
{
    protected $signature = 'makenew:repo';

    protected $description = 'Create a new repository';

    public function handle()
    {
        $name = $this->ask('Enter the model name for repository');

        $result = Pipeline::send(['name' => $name])
            ->through([
                CreateModel::class,
                CreateRepository::class,
            ])
            ->thenReturn();

        $this->info("Repository created: {$name}Repository");
    }
}