<?php

use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\rest\RestController;
use ch\makae\makaegallery\tests\SuffixRestController;
use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{


    public function test_canInstantiate()
    {
        $restApi = new RestApi();
        $this->assertTrue($restApi instanceof RestApi);
    }

    public function test_handleRequest_withNoController_throwsException()
    {
        $restApi = new RestApi();

        $this->expectException(\ch\makae\makaegallery\rest\ControllerDefinitionException::class);
        $restApi->handleRequest('/');
    }

    public function test_addController_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new RestController());
        $result = $restApi->handleRequest('/');
        $this->assertEquals("/", $result);
    }

    public function test_handlingDifferentREquests_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new RestController());
        $resultAlpha = $restApi->handleRequest('/alpha');
        $resultBeta = $restApi->handleRequest('/beta');
        $this->assertEquals('/alpha', $resultAlpha);
        $this->assertEquals('/beta', $resultBeta);
    }

    public function test_handlingRequestsDifferently_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new SuffixRestController());
        $resultAlpha = $restApi->handleRequest('/alpha');
        $this->assertEquals('/alpha-suffix', $resultAlpha);
    }

}