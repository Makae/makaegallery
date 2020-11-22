<?php

namespace ch\makae\makaegallery\rest;

abstract class RestController implements IRestController
{
    private Route $route;

    public function __construct(string $routePattern) {
        $this->route = new Route($routePattern);
    }

    public function handle(string $path)
    {
        return $path;
    }

    public function matchesPath(string $path)
    {
        return $this->route->matches($path);
    }
}
