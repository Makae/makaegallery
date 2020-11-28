<?php

namespace ch\makae\makaegallery\rest;

use PHPUnit\Framework\TestCase;

class MultiRestControllerMock extends MultiRestController {
    public ?RequestData $getResourceEndpointParams = null;
    public ?RequestData $postResourceEndpointParams = null;

    public function __construct()
    {
        parent::__construct(new RouteDeclarations([
            [new Route('/api/resource/{resource_id}'), [$this, 'getResourceEndpoint']],
            [new Route('/api/resource', "POST"), [$this, 'postResourceEndpoint']],
        ]));
    }

    public function getResourceEndpoint(RequestData $requestData): HttpResponse {
        $this->getResourceEndpointParams = $requestData;
        return new HttpResponse("get ok", HttpResponse::STATUS_OK);
    }

    public function postResourceEndpoint(RequestData $requestData): HttpResponse {
        $this->postResourceEndpointParams = $requestData;
        return new HttpResponse("post ok", HttpResponse::STATUS_CREATED);
    }

}


class MultiRestControllerTest extends TestCase
{
    public function test_emptyRoute_returns400()
    {
        $multiRestController = new MultiRestControllerMock();

        $response = $multiRestController->handle("GET", '', [], []);

        $this->assertNull($multiRestController->getResourceEndpointParams);
        $this->assertNull($multiRestController->postResourceEndpointParams);
        $this->assertEquals(HttpResponse::STATUS_BAD_REQUEST, $response->getStatus());
        $this->assertEquals("Invalid route", $response->getBody());
    }

    public function test_validRoute_callsGETHandler()
    {
        $multiRestController = new MultiRestControllerMock();

        $response = $multiRestController->handle("GET", '/api/resource/1', [], []);

        $this->assertEquals("get ok", $response->getBody());
        $this->assertNull($multiRestController->postResourceEndpointParams);
        $this->assertEquals(200, $response->getStatus());
    }

    public function test_validRoute_callsPOSTHandler()
    {
        $multiRestController = new MultiRestControllerMock();

        $response = $multiRestController->handle("POST", '/api/resource', [], []);

        $this->assertEquals("post ok", $response->getBody());
        $this->assertNull($multiRestController->getResourceEndpointParams);
        $this->assertEquals(201, $response->getStatus());
    }

    public function test_validRouteAndParams_passesParamsToHandler()
    {
        $multiRestController = new MultiRestControllerMock();

        $response = $multiRestController->handle("GET", '/api/resource/1', ['header_2'=>2], ['body_3'=>3]);

        $this->assertEquals(1, $multiRestController->getResourceEndpointParams->getParameters()['resource_id']);
        $this->assertEquals(2, $multiRestController->getResourceEndpointParams->getHeader()['header_2']);
        $this->assertEquals(3, $multiRestController->getResourceEndpointParams->getBody()['body_3']);
    }

}
