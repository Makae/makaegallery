<?php

use ch\makae\makaegallery\rest\ControllerDefinitionException;
use ch\makae\makaegallery\rest\RestApi;
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

        $this->expectException(ControllerDefinitionException::class);
        $restApi->handleRequest('/', [], []);
    }

    public function test_addController_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new SuffixRestController('', '/'));
        $result = $restApi->handleRequest('/', [], []);
        $this->assertEquals("/", $result->getBody());
    }

    public function test_handlingDifferentRequests_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new SuffixRestController('', '/'));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $resultBeta = $restApi->handleRequest('/beta', [], []);
        $this->assertEquals('/alpha', $resultAlpha->getBody());
        $this->assertEquals('/beta', $resultBeta->getBody());
    }

    public function test_handlingRequestsDifferently_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new SuffixRestController("-suffix", "/"));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $this->assertEquals('/alpha-suffix', $resultAlpha->getBody());
    }

    public function test_handlingRequestsByDifferentController_works()
    {
        $restApi = new RestApi();
        $restApi->addController(new SuffixRestController("-suffix", '/first'));
        $restApi->addController(new SuffixRestController("-other-suffix", '/second'));
        $this->assertEquals('/first-suffix', $restApi->handleRequest('/first', [], [])->getBody());
        $this->assertEquals('/second-other-suffix', $restApi->handleRequest('/second', [], [])->getBody());
    }

}
