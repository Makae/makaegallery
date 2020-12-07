<?php

namespace ch\makae\makaegallery\rest;

class Route
{
    private string $method;
    private ?array $route;

    public function __construct(string $routePattern, string $method = "GET")
    {
        $this->method = $method;
        if (!preg_match('/^(\/|(\/({[^{}]+}|[^}\/{]+))+)\/?$/', $routePattern)) {
            throw new InvalidRouteException("The route pattern " . $routePattern . " is invalid");
        }

        $this->route = $this->getRoute($routePattern);
    }

    private function getRoute(string $routePath): array
    {
        if (substr($routePath, -1) === '/') {
            $routePath = substr($routePath, 0, -1);
        }
        $parts = explode('/', $routePath);
        $mapping = [];
        foreach ($parts as $key => $value) {
            if (strpos($value, '{') === false) {
                continue;
            }
            $name = substr($value, 1, -1);
            $mapping[$name] = $key;
        }

        // /locations/{id} -> /locations/[a-zA-Z0-9-_]\/?
        $pattern = preg_replace('/{[^{}]+}/', '[a-zA-Z0-9-_]+', $routePath);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern .= '\/?';

        return [
            'mapping' => $mapping,
            'pattern' => '/' . $pattern . '/'
        ];
    }

    public function matches(string $path, string $method = "GET"): bool
    {
        if ($this->method !== $method) {
            return false;
        }
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

    public function getMethod(): string
    {
        return $this->method;
    }
}

class InvalidRouteException extends \Exception
{
}
