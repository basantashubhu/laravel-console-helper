<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Illuminate\Console\Command;

class MakeComposerRepository extends Command
{
    protected $signature = 'make:composer-repository {type?}';

    protected $description = 'Create a new composer repository';

    public function handle()
    {
        $type = $this->argument('type') ?: $this->choice('Select the repository type', ['vcs', 'path', 'artifact'], 0);

        $src = $this->ask('Enter the repository source for ' . $type);

        $this->info("Creating a new composer repository: {$src}");

        $this->createRepository($type, $src);

        $this->info("Composer repository created: {$src}");

        return 0;
    }

    protected function createRepository($type, $src)
    {
        $path = base_path('composer.json');

        $composer = json_decode(file_get_contents($path), true);

        $repositories = $composer['repositories'] ?? [];

        $repositories[] = [
            'type' => $type,
            'url' => $src,
        ];

        $composer['repositories'] = $repositories;

        file_put_contents($path, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}