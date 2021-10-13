<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\rest\IHttpErrorResponseHandler;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\security\Authentication;
use Excepton;

class App
{
  private IGalleryRepository $galleryRepository;
  private Authentication $auth;
  private RestApi $restApi;
  private IHttpErrorResponseHandler $errorResponseHandler;

  public function __construct(
    Authentication $authentication,
    IGalleryRepository $galleryRepository,
    RestApi $restApi,
    IHttpErrorResponseHandler $errorResponseHandler
  )
  {
    $this->galleryRepository = $galleryRepository;
    $this->auth = $authentication;
    $this->restApi = $restApi;
    $this->errorResponseHandler = $errorResponseHandler;
  }

  public function processRequest($requestMethod, $requestURI, $header, $body)
  {
    $uri = Utils::getRequestUri($requestURI);
    $this->setHeaders();
    try {
      $response = $this->restApi->handleRequest($requestMethod, '/' . $uri, $header, $body);
    } catch (\Exception $exception) {
      $response = $this->errorResponseHandler->handleException($exception);
    }
    http_response_code($response->getStatus());
    echo $response->getBody();
    exit;
  }

  private function setHeaders()
  {
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: " .  CORS_ALLOWED_ORIGINS);
    header("Access-Control-Allow-Methods: ". CORS_ALLOWED_METHODS);
    header('Access-Control-Allow-Headers: token, Content-Type');
  }

  public function getGalleryRepository(): IGalleryRepository
  {
    return $this->galleryRepository;
  }

  public function getAuth(): Authentication
  {
    return $this->auth;
  }

  public function getRestApi(): RestApi
  {
    return $this->restApi;
  }

}
