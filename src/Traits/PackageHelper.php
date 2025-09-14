<?php

namespace Basanta\LaravelConsoleHelper\Traits;

use Illuminate\Support\Arr;

trait PackageHelper
{
    protected array $composerJson = [];


    /**
     * Load the composer.json file for a given package.
     *
     * @param string $package The name of the package.
     * @return void
     */
    public function loadComposerJson($package)
    {
        if (isset($this->composerJson[$package])) {
            return;
        }

        $path = $package != 'main' ? base_path('vendor/' . $package . '/composer.json') : base_path('composer.json');

        if (!file_exists($path)) {
            $this->composerJson[$package] = null;

            return;
        }

        $this->composerJson[$package] = json_decode(file_get_contents($path), true);

        // resolve namespace
        $this->resolvePackageNamespace($package);
    }


    /**
     * Resolves the namespace for a given package.
     *
     * @param string $package The name of the package.
     * @return void
     */
    public function resolvePackageNamespace($package)
    {
        $autoload = Arr::get($this->composerJson[$package], 'autoload.psr-4');
        $this->composerJson[$package]['namespace'] = key($autoload);
    }


    /**
     * Get the full path for the PHP class file.
     *
     * @param string $namespace The namespace for the class.
     * @param string $package The name of the package.
     * @return string The full path for the PHP class file.
     */
    public function getClassPathForPackage($namespace, $package)
    {
        $this->loadComposerJson($package);

        $replace = Arr::get($this->composerJson[$package], 'autoload.psr-4', []);
        $replace['\\'] = '/';
        $path = strtr($namespace, $replace);

        $path = $package != 'main' ? 'vendor/' . $package . '/' . $path : $path;

        return $path;
    }
}