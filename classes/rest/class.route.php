<?php

namespace ch\makae\makaegallery\rest;

class Route
{
    private ?array $route;

    public function __construct(string $routePattern)
    {
        if (!preg_match('/^(\/|(\/({[^{}]+}|[^}\/{]+))+)$/', $routePattern)) {
            throw new InvalidRouteException("The route pattern " . $routePattern . " is invalid");
        }

        $this->route = $this->getRoute($routePattern);
    }

    private function getRoute(string $route): array
    {
        $parts = explode('/', $route);
        $mapping = [];
        foreach ($parts as $key => $value) {
            if (strpos($value, '{') === false) {
                continue;
            }
            $name = substr($value, 1, -1);
            $mapping[$name] = $key;
        }

        // /locations/{id} -> /locations/[a-zA-Z0-9-_]
        $pattern = preg_replace('/{[^{}]+}/', '[a-zA-Z0-9-_]+', $route);
        $pattern = str_replace('/', '\/', $pattern);
        return [
            'mapping' => $mapping,
            'pattern' => '/' . $pattern . '/'
        ];
    }

    public function matches(string $path): bool
    {
        if (!preg_match($this->route['pattern'], $path)) {
            return false;
        }
        return true;
    }

    public function getParameters(string $path)
    {
        $parts = explode('/', $path);
        $mapping = [];
        foreach ($this->route['mapping'] as $name => $value) {
            $mapping[$name] = $parts[$value];
        }

        return $mapping;
    }
}

class InvalidRouteException extends \Exception
{
}
