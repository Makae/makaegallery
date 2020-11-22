<?php

namespace ch\makae\makaegallery\rest;

use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function test_emptyRoute_throwsException()
    {
        $this->expectException(InvalidRouteException::class);
        new Route("");
    }

    public function test_rootRoute_works()
    {
        $route = new Route("/");
        $this->assertTrue($route->matches("/"));
    }

    public function test_nonParameterRoute_works()
    {
        $route = new Route("/locations");
        $this->assertTrue($route->matches("/locations"));
    }

    public function test_parameterRoute_works()
    {
        $route = new Route("/locations/{id}");
        $this->assertTrue($route->matches("/locations/1"));
    }

    public function test_parameterValuesUuid_works()
    {
        $route = new Route("/locations/{uuid}");
        $parameters = $route->getParameters("/locations/85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
        $this->assertEquals($parameters['uuid'], "85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
    }

    public function test_multipleParameterValues_works()
    {
        $route = new Route("/locations/{uuid}/{other}");
        $parameters = $route->getParameters("/locations/85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1/foobar");
        $this->assertEquals($parameters['uuid'], "85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
        $this->assertEquals($parameters['other'], "foobar");
    }
}
