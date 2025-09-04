<?php

namespace Basanta\LaravelConsoleHelper\Commands;

use Basanta\LaravelConsoleHelper\Traits\ArrayVariable;
use Basanta\LaravelConsoleHelper\Traits\PackageHelper as PackageTrait;

class PackageHelper extends \Illuminate\Console\Command
{
    use PackageTrait, ArrayVariable;

    protected $signature = 'package:make {create?}';

    protected $description = 'Create anything for package development';

    public function handle()
    {
        $packageName = $this->ask('Enter the package name [username/repo]');

        $srcDir = base_path("vendor/$packageName/src");

        $create = $this->option('create') ?: $this->choice('What do you want to create?', [
            'Controller', 'Model', 'Migration'
        ], 0);

        return 0;
    }
}