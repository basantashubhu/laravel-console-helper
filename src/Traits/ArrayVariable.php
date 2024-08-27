<?php

namespace Basanta\LaravelConsoleHelper\Traits;

trait ArrayVariable
{
    protected array $arrayVar = [];

    public function addVariable($key, $value)
    {
        $this->arrayVar[$key] = $value;
    }

    public function variable($key, $default = null)
    {
        return $this->arrayVar[$key] ?? $default;
    }

    public function variables(): array
    {
        return $this->arrayVar;
    }
}