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

    public function getMatchingRouteDeclaration(string $path, string $method="GET"): ?array {
        foreach ($this->declarations as $declaration) {
            if($declaration[0]->matches($path, $method)) {
                return $declaration;
            }
        }
        return null;
    }

}
