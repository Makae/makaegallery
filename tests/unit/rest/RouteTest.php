<?php

namespace ch\makae\makaegallery\rest;

use ch\makae\makaegallery\security\Authentication;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function test_emptyRoute_throwsException()
    {
        $this->expectException(InvalidRouteException::class);
        new GETRoute("");
    }

    public function test_rootRoute_works()
    {
        $route = new GETRoute("/");
        $this->assertTrue($route->matches('GET', "/"));
    }

    public function test_nonParameterRoute_works()
    {
        $route = new GETRoute("/locations");
        $this->assertTrue($route->matches('GET', "/locations"));
    }

    public function test_parameterRoute_works()
    {
        $route = new GETRoute("/locations/{id}");
        $this->assertTrue($route->matches('GET', "/locations/1"));
    }

    public function test_parameterRouteTralingSlash_works()
    {
        $route = new GETRoute("/locations/{uuid}/");
        $this->assertTrue($route->matches('GET', "/locations/foo_bar/"));
    }

    public function test_matches_withQuery_works() {
        $route = new GETRoute("/locations/{uuid}/");
        $this->assertTrue($route->matches('GET', "/locations/foo_bar/?query=value"));
    }

    public function test_matches_withDetailedQuery_works()
    {
        $route = new POSTRoute('/api/gallery/{gallery_id}/image');
        $this->assertTrue(
            $route->matches(
                'POST',
                '/api/gallery/__my_gallery/image/?ajax=1&nonce=05b7dfa6ec47d3f6899ef1028b60127f&action=upload_images&galleryid=__my_gallery'
            )
        );
    }

    public function test_matches_withTrailingQuestionMark_works() {
        $route = new GETRoute("/locations/{uuid}/");
        $this->assertTrue($route->matches('GET', "/locations/foo_bar/?"));
    }

    public function test_matches_withSubPath_doesNotWork() {
        $route = new GETRoute("/locations/{uuid}/");
        $this->assertFalse($route->matches('GET', "/locations/foo_bar/subpath"));
    }

    public function test_gallery_route()
    {
        $url = "/api/gallery/foo_bar";
        $route = new GETRoute("/api/gallery/{gallery_id}");
        $this->assertTrue($route->matches('GET', $url));

        $parameters = $route->getParameters($url);
        $this->assertEquals($parameters['gallery_id'], "foo_bar");
    }

    public function test_route_getParameterTrailingSlash_works()
    {
        $route = new GETRoute("/locations/{uuid}");
        $parameters = $route->getParameters("/locations/foo_bar/");
        $this->assertEquals($parameters['uuid'], "foo_bar");
    }

    public function test_parameterValuesUuid_works()
    {
        $route = new GETRoute("/locations/{uuid}");
        $parameters = $route->getParameters("/locations/85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
        $this->assertEquals($parameters['uuid'], "85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
    }


    public function test_multipleParameterValues_works()
    {
        $route = new GETRoute("/locations/{uuid}/{other}");
        $parameters = $route->getParameters("/locations/85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1/foobar");
        $this->assertEquals($parameters['uuid'], "85a00ecd-a19c-426d-a3fe-baa8a6f3f0b1");
        $this->assertEquals($parameters['other'], "foobar");
    }
}
