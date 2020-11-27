<?php

namespace ch\makae\makaegallery\rest;

abstract class SimpleRestController implements IRestController
{
    private Route $route;

    public function __construct(string $routePattern) {
        $this->route = new Route($routePattern);
    }

    public function handle(string $method, string $path, array $header, array $body): HttpResponse
    {
        return new HttpResponse($path);
    }

    public function matchesPath(string $path, string $method="GET")
    {
        return $this->route->matches($path);
    }

    protected function getRequestData(string $path, array $header, array $body): RequestData
    {
        return new RequestData($this->route->getParameters($path), $header, $body);
    }
}
