<?php


namespace ch\makae\makaegallery\rest;


class RouteDeclarations
{
    private array $declarations;

    public function __construct(array $declarations)
    {
        $this->declarations = $declarations;
    }

    public function addMapping(Route $route, callable $handler) {
        $this->declarations[] =  [$route, $handler];
    }

    public function getMatchingRouteDeclaration(string $method, string $path): ?array {
        foreach ($this->declarations as $declaration) {
            if($declaration[0]->matches($method, $path)) {
                return $declaration;
            }
        }
        return null;
    }

}
