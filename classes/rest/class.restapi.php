<?php

namespace ch\makae\makaegallery\rest;

class RestApi
{
    private array $controllers = [];

    public function handleRequest(string $path, array $header, array $body): HttpResponse
    {
        $controller = $this->getMatchingController($path);
        if (is_null($controller)) {
            throw new ControllerDefinitionException("Can not find suitable Controller");
        }
        return $controller->handle($path, $header, $body);
    }

    private function getMatchingController(string $path): ?IRestController
    {
        foreach ($this->controllers as $controller) {
            if ($controller->matchesPath($path)) {
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
