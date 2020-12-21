<?php

namespace ch\makae\makaegallery\rest;

use ch\makae\makaegallery\Authentication;

abstract class SimpleRestController implements IRestController
{
    private Route $route;

    public function __construct(string $method, string $routePattern, int $accessLevel = Authentication::ACCESS_LEVEL_ADMIN)
    {
        $this->route = new Route($method, $routePattern, $accessLevel);
    }

    public function handle(string $method, string $path, array $header, array $body): HttpResponse
    {
        return new HttpResponse($path);
    }

    public function getAccessLevel(string $method, string $path): int
    {
        return $this->route->getAccessLevel();
    }

    public function matchesPath(string $method, string $path): bool
    {
        return $this->route->matches($method, $path);
    }

    protected function getRequestData(string $path, array $header, array $body): RequestData
    {
        return new RequestData($this->route->getParameters($path), $header, $body);
    }

}
