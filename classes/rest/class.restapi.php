<?php

namespace ch\makae\makaegallery\rest;

use ch\makae\makaegallery\security\Authentication;

class RestApi
{
  private array $controllers = [];
  private string $apiUrl;
  private Authentication $authentication;

  public function __construct(string $apiUrl, Authentication $authentication)
  {
    $this->apiUrl = $apiUrl;
    $this->authentication = $authentication;
  }

  public function getUrl(): string
  {
    return $this->apiUrl;
  }

  public function handleRequest(string $method, string $path, array $header = [], array $body = []): HttpResponse
  {
    if($header['Content-Type'] === 'application/json') {
      $body = json_decode(file_get_contents('php://input'), true);
    }

    $controller = $this->getMatchingController($method, $path);

    if (is_null($controller)) {
      throw new ControllerDefinitionException("Can not find suitable Controller");
    }
    return $controller->handle($method, $path, $header, $body);
  }

  private function getMatchingController(string $method, string $path): ?IRestController
  {
    foreach ($this->controllers as $controller) {
      if (!$controller->matchesPath($method, $path)) {
        continue;
      }
      if (!$this->authentication->hasAccessForLevel($controller->getAccessLevel($method, $path))) {
        die(var_dump($method, $path, $controller->getAccessLevel($method, $path)));
        throw new RestAccessLevelException("Can not access $method $path");
      }
      return $controller;

    }
    return null;
  }

  public function addController(IRestController $controller)
  {
    $this->controllers[] = $controller;
  }

}

class RestAccessLevelException extends \Exception
{
}

