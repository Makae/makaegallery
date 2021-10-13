<?php

namespace ch\makae\makaegallery\rest\controllers;

use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\IRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;

abstract class MultiRestController implements IRestController
{
  private RouteDeclarations $routeDeclarations;

  public function __construct(RouteDeclarations $routeDeclarations)
  {
    $this->routeDeclarations = $routeDeclarations;
  }

  public function matchesPath(string $method, string $path): bool
  {
    return !is_null($this->routeDeclarations->getMatchingRouteDeclaration($method, $path));
  }

  public function handle(string $method, string $path, array $header, array $body): HttpResponse
  {
    $routeDeclaration = $this->routeDeclarations->getMatchingRouteDeclaration($method, $path);

    if (is_null($routeDeclaration)) {
      return new HttpResponse("Invalid route", HttpResponse::STATUS_NOT_FOUND);
    }
    list($route, $handler) = $routeDeclaration;
    return call_user_func($handler, new RequestData($route->getParameters($path), $header, $body));
  }

  public function getAccessLevel(string $method, string $path): int
  {
    return $this->routeDeclarations->getMatchingRouteDeclaration($method, $path)[0]->getAccessLevel();
  }

}
