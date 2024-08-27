<?php

namespace Basanta\LaravelConsoleHelper\Traits;

use Illuminate\Support\Arr;

trait PackageHelper
{
    protected array $composerJson = [];

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

    public function resolvePackageNamespace($package)
    {
        $autoload = Arr::get($this->composerJson[$package], 'autoload.psr-4');
        $this->composerJson[$package]['namespace'] = key($autoload);
    }

    public function getClassPathForPackage($namespace, $package)
    {
        $this->loadComposerJson($package);

        $path = strtr($namespace, $tr = [
            ...Arr::get($this->composerJson[$package], 'autoload.psr-4', []),
            '\\' => '/',
        ]);

        $path = $package != 'main' ? 'vendor/' . $package . '/' . $path : $path;

        return $path;
    }
}