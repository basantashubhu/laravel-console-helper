<?php
namespace Basanta\LaravelConsoleHelper\App\Pipeline;

use Illuminate\Support\Facades\Artisan;

class CreateModel
{
    public function handle(array $data, \Closure $next)
    {
        $name = $data['name'];

        $exists = class_exists("App\\Models\\{$name}");

        if(!$exists) {
            Artisan::call('make:model', ['name' => $name]);
        }

        return $next($data);
    }
}