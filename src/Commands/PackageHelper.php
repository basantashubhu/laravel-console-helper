<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Basanta\LaravelConsoleHelper\Traits\ArrayVariable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class PackageHelper extends \Illuminate\Console\Command
{
    use ArrayVariable;
    use \Basanta\LaravelConsoleHelper\Traits\PackageHelper;

    protected $signature = 'package:make {create?} {--force}';

    protected $description = 'Create anything for package development';

    public function handle()
    {
        $package = $this->ask('Enter the package name [username/repo]');

        if(!str_contains($package, '/')) {
            $package = $this->choice('Choose package', array_map(function ($dir) use ($package) {
                return Str::after($dir, "vendor/");
            }, glob(base_path("vendor/$package/*"), GLOB_ONLYDIR)));
        }

        $create = $this->argument('create') ?: $this->choice('What do you want to create?', [
            'controller' => 'Controller',
            'model' => 'Model',
            'migration' => 'Migration',
            'request' => 'Request',
            'command' => 'Command'
        ], 0);

        $name = str_replace('\\', '/', $this->ask("Enter the $create name"));

        $this->addVariable('create', $create);
        $this->addVariable('name', $name);
        $this->addVariable('package', $package);

        Artisan::call("make:$create", [
            'name' => $name,
        ]);

        $this->fillStub();
        $this->moveFile();

        return 0;
    }

    private function moveFile()
    {
        [$source, $destination] = $this->sourceDestination();
        if (!file_exists($source)) {
            $this->error("Source file does not exist: $source");
            return;
        }

        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (file_exists($destination) && !$this->option('force')) {
            $this->error("Destination file already exists: $destination");
            return;
        }

        rename($source, $destination);

        $this->info("Created: ". strstr($destination, '/src/'));
    }

    private function sourceDestination()
    {
        $create = $this->variable('create');
        $name = $this->variable('name');
        $package = $this->variable('package');

        switch ($create) {
            case 'controller':
                $source = app_path("Http/Controllers/$name.php");
                $destination = base_path("vendor/$package/src/Http/Controllers/$name.php");
                break;
            case 'model':
                $source = app_path("Models/$name.php");
                $destination = base_path("vendor/$package/src/Models/$name.php");
                break;
            case 'migration':
                $source = database_path("migrations/$name.php");
                $destination = base_path("vendor/$package/database/migrations/$name.php");
                break;
            case 'request':
                $source = app_path("Http/Requests/$name.php");
                $destination = base_path("vendor/$package/src/Http/Requests/$name.php");
                break;
            case 'command':
                $source = app_path("Console/Commands/$name.php");
                $destination = base_path("vendor/$package/src/Console/Commands/$name.php");
                break;
            default:
                $this->error("Unknown create type: $create");
                exit(1);
        }

        return [$source, $destination];
    }

    private function fillStub()
    {
        [$source] = $this->sourceDestination();

        $this->loadComposerJson($package = $this->variable('package'));
        $namespace = $this->composerJson[$package]['namespace'];

        file_put_contents($source, strtr(file_get_contents($source), [
            'App\\' => $namespace
        ]));
    }
}
