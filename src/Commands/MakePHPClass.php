<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Illuminate\Console\Command;
use Basanta\LaravelConsoleHelper\Traits\ArrayVariable;
use Basanta\LaravelConsoleHelper\Traits\PackageHelper;

class MakePHPClass extends Command
{
    use PackageHelper, ArrayVariable;

    protected $signature = 'make:class {name?} {--package=}';

    protected $description = 'Create a new PHP class';

    public function handle()
    {
        $name = $this->argument('name') ?: $this->ask('Enter the class name');
        $namespace = $this->ask('Enter the namespace for the class', 'App');
        $extends = $this->ask('Enter the class to extend');
        $implements = $this->ask('Enter the interfaces to implement');

        $this->addVariable('{{ className }}', $name);
        $this->addVariable('{{ namespace }}', $namespace);
        $this->addVariable('{{ extends }}', $extends ? " extends $extends" : '');
        $this->addVariable('{{ implements }}', $implements ? " implements $implements" : '');

        $this->info("Creating a new PHP class: {$namespace}\\{$name}");

        $this->createClass($name, $namespace);

        $this->info("PHP class created: {$namespace}\\{$name}");

        return 0;
    }

    protected function createClass($name, $namespace)
    {
        $path = $this->getClassPath($name, $namespace);

        if ($this->alreadyExists($path)) {
            $this->error('Class already exists!');

            return;
        }

        $this->makeDirectory($path);

        file_put_contents($path, $this->compileClassStub());
    }

    protected function compileClassStub()
    {
        $stub = file_get_contents(__DIR__ . '/../stub/php-class.stub');

        $stub = strtr($stub, $this->variables());

        $stub = preg_replace('/{{([^}}].*)}}/', '', $stub); // remove unused variables
        
        return $stub;
    }

    protected function getClassPath($name, $namespace)
    {
        if($forPackage = $this->option('package')) {
            $path = $this->getClassPathForPackage($namespace, $forPackage);
        } else {
            $path = $this->getClassPathForPackage($namespace, 'main');
        }

        return base_path($path) . '/' . $name . '.php';
    }

    protected function makeDirectory($path)
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function alreadyExists($path)
    {
        return file_exists($path);
    }
}