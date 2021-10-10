<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\rest\GETRoute;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\MultiRestController;
use ch\makae\makaegallery\rest\POSTRoute;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\security\Security;

class AuthenticationRestController extends MultiRestController
{
  private Security $security;
  private Authentication $authentication;

  public function __construct(Security $security, Authentication $authentication)
  {
    parent::__construct(new RouteDeclarations([
      [new POSTRoute('/api/auth/login', Authentication::ACCESS_LEVEL_PUBLIC), [$this, 'loginUser']],
      [new POSTRoute('/api/auth/logout', Authentication::ACCESS_LEVEL_PUBLIC), [$this, 'logoutUser']]
    ]));
    $this->security = $security;
    $this->authentication = $authentication;
  }

  public function loginUser(RequestData $requestData): HttpResponse
  {
    $dto = $requestData->getBody();
    $name = $dto['name'];
    $password = $dto['password'];

    if ($this->authentication->login($name, $password)) {
      return HttpResponse::responseOK();
    } else {
      return new HttpResponse('', HttpResponse::STATUS_UNAUTHORIZED);
    }
  }

  public function logoutUser(RequestData $requestData): HttpResponse
  {
    $this->authentication->logout();
    return HttpResponse::responseOK();
  }
}
