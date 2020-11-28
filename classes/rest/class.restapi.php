<?php

namespace ch\makae\makaegallery\rest;

class RestApi
{
    private array $controllers = [];

    public function handleRequest(string $path, array $header, array $body): HttpResponse
    {
        $method = isset($header['REQUEST_METHOD']) ? $header['REQUEST_METHOD'] : "GET";
        $controller = $this->getMatchingController($path, $method);
        if (is_null($controller)) {
            throw new ControllerDefinitionException("Can not find suitable Controller");
        }
        return $controller->handle($method, $path, $header, $body);
    }

    private function getMatchingController(string $path, string $method="GET"): ?IRestController
    {
        foreach ($this->controllers as $controller) {
            if ($controller->matchesPath($path, $method)) {
                return $controller;
            }
        }
        return null;
    }

    public function addController(IRestController $controller)
    {
        $this->controllers[] = $controller;
    }
}
