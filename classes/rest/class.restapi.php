<?php

namespace ch\makae\makaegallery\rest;

class RestApi
{
    private ?IRestController $controller= null;

    public function handleRequest(string $path)
    {
        if(is_null($this->controller)) {
            throw new ControllerDefinitionException("Can not find suitable Controller");
        }
        return $this->controller->handle($path);
    }

    public function addController(IRestController $controller)
    {
        $this->controller = $controller;
    }
}
