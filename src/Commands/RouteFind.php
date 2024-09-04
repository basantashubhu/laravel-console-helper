<?php

namespace Basanta\LaravelConsoleHelper\Commands;
use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class RouteFind extends Command
{
    protected $signature = 'route:find {route? : URI to find}';

    protected $description = 'Find exact implementation of a route by URI. Copy paste the uri from the browser. Use * as wildcard.';


    public function handle()
    {
        $routeToFind = $this->argument('route') ?: $this->ask('Enter route name');

        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $results = [];

        foreach ($routes as $route) {
            if (Str::is($routeToFind, $route->uri)) {
                $method = implode('|', $route->methods);
                $controller = Str::before($route->action['controller'], '@');
                $action = Str::after($route->action['controller'], '@');

                $refClass = new ReflectionClass($controller);
                $refMethod = $refClass->getMethod($action);
                $lineNumber = $refMethod->getStartLine();

                $results[] = [$method, $route->uri, "{$controller}.php:$lineNumber"];
            }
        }

        if (empty($results)) {
            $this->error("Route not found: $routeToFind");
        } else {
            $this->table(['Method', 'URI', 'Controller'], $results);
        }
    }
}