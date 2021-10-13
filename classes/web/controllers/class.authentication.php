<?php


namespace ch\makae\makaegallery\web\controllers;

use ch\makae\makaegallery\rest\GETRoute;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\controllers\MultiRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;
use ch\makae\makaegallery\security\Authentication;

class AuthenticationRestController extends MultiRestController
{
  private Authentication $authentication;

  public function __construct(Authentication $authentication)
  {
    parent::__construct(new RouteDeclarations([
      [new GETRoute('/api/auth/status', Authentication::ACCESS_LEVEL_RESTRICTED), [$this, 'authenticationStatus']]
    ]));
    $this->authentication = $authentication;
  }

  public function authenticationStatus(RequestData $requestData): HttpResponse
  {
    if ($this->authentication->isAuthenticated()) {
      return HttpResponse::responseOK();
    }
    return HttpResponse::responseUnauthorized('Please provide BasicAuth headers');
  }

}
