<?php

use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\rest\ControllerDefinitionException;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\tests\AuthenticationHelper;
use ch\makae\makaegallery\tests\SuffixSimpleRestController;
use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{

    public function test_canInstantiate()
    {
        $restApi = $this->getRestApi();
        $this->assertTrue($restApi instanceof RestApi);
    }

    private function getRestApi($user = "admin", $password = "123456")
    {
        $auth = AuthenticationHelper::getAuthenticationMock([]);
        $auth->login($user, $password);
        return new RestApi(WWW_BASE . '/api', $auth);
    }

    public function test_handleRequest_withNoController_throwsException()
    {
        $restApi = $this->getRestApi();

        $this->expectException(ControllerDefinitionException::class);
        $restApi->handleRequest('/', [], []);
    }

    public function test_addController_works()
    {
        $restApi = $this->getRestApi();
        $restApi->addController(new SuffixSimpleRestController('', 'GET', '/'));
        $result = $restApi->handleRequest('/', [], []);
        $this->assertEquals("/", $result->getBody());
    }

    public function test_handlingDifferentRequests_works()
    {
        $restApi = $this->getRestApi();
        $restApi->addController(new SuffixSimpleRestController('', 'GET', '/'));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $resultBeta = $restApi->handleRequest('/beta', [], []);
        $this->assertEquals('/alpha', $resultAlpha->getBody());
        $this->assertEquals('/beta', $resultBeta->getBody());
    }

    public function test_handlingRequestsDifferently_works()
    {
        $restApi = $this->getRestApi();
        $restApi->addController(new SuffixSimpleRestController("-suffix", 'GET', "/"));
        $resultAlpha = $restApi->handleRequest('/alpha', [], []);
        $this->assertEquals('/alpha-suffix', $resultAlpha->getBody());
    }

    public function test_handlingRequestsByDifferentController_works()
    {
        $restApi = $this->getRestApi();
        $restApi->addController(new SuffixSimpleRestController("-suffix", 'GET', '/first'));
        $restApi->addController(new SuffixSimpleRestController("-other-suffix", 'GET', '/second'));
        $this->assertEquals('/first-suffix', $restApi->handleRequest('/first', [], [])->getBody());
        $this->assertEquals('/second-other-suffix', $restApi->handleRequest('/second', [], [])->getBody());
    }

    public function test_accessRestrictedRoute_returnsHttpForbidden()
    {
        $restApi = $this->getRestApi("user", "123456");
        $restApi->addController(new SuffixSimpleRestController('', 'GET', '/admin'));
        $restApi->addController(new SuffixSimpleRestController('+asdf', 'GET', '/user', Authentication::ACCESS_LEVEL_USER));
        $restApi->addController(new SuffixSimpleRestController('+qwertz', 'GET', '/guest', Authentication::ACCESS_LEVEL_GUEST));

        $resultAdminRoute = $restApi->handleRequest('/admin');
        $this->assertEquals("Access forbidden!", $resultAdminRoute->getBody());
        $this->assertEquals(403, $resultAdminRoute->getStatus());

        $resultUserRoute = $restApi->handleRequest('/user');
        $this->assertEquals("/user+asdf", $resultUserRoute->getBody());
        $this->assertEquals(\ch\makae\makaegallery\rest\HttpResponse::STATUS_OK, $resultUserRoute->getStatus());

        $resultUserRoute = $restApi->handleRequest('/guest');
        $this->assertEquals("/guest+qwertz", $resultUserRoute->getBody());
        $this->assertEquals(\ch\makae\makaegallery\rest\HttpResponse::STATUS_OK, $resultUserRoute->getStatus());
    }

}
