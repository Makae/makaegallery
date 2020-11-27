<?php

namespace ch\makae\makaegallery\rest;

abstract class MultiRestController implements IRestController
{
    private RouteDeclarations $routeDeclarations;

    public function __construct(RouteDeclarations $routeDeclarations)
    {
        $this->routeDeclarations = $routeDeclarations;
    }

    public function matchesPath(string $path, string $method="GET") {
        return !is_null($this->routeDeclarations->getMatchingRouteDeclaration($path, $method));
    }

    public function handle(string $method, string $path, array $header, array $body): HttpResponse
    {
        $routeDeclaration = $this->routeDeclarations->getMatchingRouteDeclaration($path, $method);
        if (is_null($routeDeclaration)) {
            return new HttpResponse("Invalid route", 400);
        }
        list($route, $handler) = $routeDeclaration;
        return call_user_func($handler, new RequestData($route->getParameters($path), $header, $body));
    }
}
