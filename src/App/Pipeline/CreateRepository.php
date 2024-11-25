<?php
namespace Basanta\LaravelConsoleHelper\App\Pipeline;

use Illuminate\Support\Facades\Artisan;

class CreateRepository
{
    public function handle(array $data, \Closure $next)
    {
        $name = $data['name'];
        $namespace = "App\\Repositories";
        $repo = "{$name}Repository";

        Artisan::call('make:class', [
            'name' => $repo,
            '--namespace' => $namespace,
        ]);

        return $next($data);
    }
}