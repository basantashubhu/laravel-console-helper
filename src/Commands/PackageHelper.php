<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Basanta\LaravelConsoleHelper\Traits\ArrayVariable;
use Illuminate\Support\Facades\Artisan;

class PackageHelper extends \Illuminate\Console\Command
{
    use ArrayVariable;

    protected $signature = 'package:make {create?} {--force}';

    protected $description = 'Create anything for package development';

    public function handle()
    {
        $package = $this->ask('Enter the package name [username/repo]');

        $create = $this->argument('create') ?: $this->choice('What do you want to create?', [
            'controller' => 'Controller',
            'model' => 'Model',
            'migration' => 'Migration',
            'request' => 'Request',
        ], 0);

        $name = str_replace('\\', '/', $this->ask("Enter the $create name"));

        $this->addVariable('create', $create);
        $this->addVariable('name', $name);
        $this->addVariable('package', $package);

        Artisan::call("make:$create", [
            'name' => $name,
        ]);

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

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $destinationFile = $destination . '/' . basename($source);
        if (file_exists($destinationFile) && !$this->option('force')) {
            $this->error("Destination file already exists: $destinationFile");
            return;
        }

        rename($source, $destinationFile);

        $this->info("Created: ". strstr($destinationFile, '/src/'));
    }

    private function sourceDestination()
    {
        $create = $this->variable('create');
        $name = $this->variable('name');
        $package = $this->variable('package');

        $source;
        $destination;

        switch ($create) {
            case 'controller':
                $source = app_path("Http/Controllers/$name.php");
                $destination = base_path("vendor/$package/src/Http/Controllers");
                break;
            case 'model':
                $source = app_path("Models/$name.php");
                $destination = base_path("vendor/$package/src/Models");
                break;
            case 'migration':
                $source = database_path("migrations/$name.php");
                $destination = base_path("vendor/$package/database/migrations");
                break;
            case 'request':
                $source = app_path("Http/Requests/$name.php");
                $destination = base_path("vendor/$package/src/Http/Requests");
                break;
            default:
                $this->error("Unknown create type: $create");
                exit(1);
        }

        return [$source, $destination];
    }
}