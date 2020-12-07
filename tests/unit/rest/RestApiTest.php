<?php

use ch\makae\makaegallery\rest\ControllerDefinitionException;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\tests\SuffixSimpleRestController;
use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{
    public function test_canInstantiate()
    {
        $restApi = new RestApi(WWW_BASE . '/api');
        $this->assertTrue($restApi instanceof RestApi);
    }

    public function test_handleRequest_withNoController_throwsException()
    {
        $restApi = new RestApi(WWW_BASE . '/api');

        $this->expectException(ControllerDefinitionException::class);
        $restApi->handleRequest('/', [], []);
    }

    public function test_addController_works()
    {
        $restApi = new RestApi(WWW_BASE . '/api');
        $restApi->addController(new SuffixSimpleRestController('', '/'));
        $result = $restApi->handleRequest('/', [], []);
        $this->assertEquals("/", $result->getBody());
    }

    public function test_handlingDifferentRequests_works()
    {
        $restApi = new RestApi(WWW_BASE . '/api');
        $restApi->addController(new SuffixSimpleRestController('', '/'));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $resultBeta = $restApi->handleRequest('/beta', [], []);
        $this->assertEquals('/alpha', $resultAlpha->getBody());
        $this->assertEquals('/beta', $resultBeta->getBody());
    }

    public function test_handlingRequestsDifferently_works()
    {
        $restApi = new RestApi(WWW_BASE . '/api');
        $restApi->addController(new SuffixSimpleRestController("-suffix", "/"));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $this->assertEquals('/alpha-suffix', $resultAlpha->getBody());
    }

    public function test_handlingRequestsByDifferentController_works()
    {
        $restApi = new RestApi(WWW_BASE . '/api');
        $restApi->addController(new SuffixSimpleRestController("-suffix", '/first'));
        $restApi->addController(new SuffixSimpleRestController("-other-suffix", '/second'));
        $this->assertEquals('/first-suffix', $restApi->handleRequest('/first', [], [])->getBody());
        $this->assertEquals('/second-other-suffix', $restApi->handleRequest('/second', [], [])->getBody());
    }

}
