<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\rest\ControllerDefinitionException;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\security\Authentication;

class App
{
  private GalleryRepository $galleryRepository;
  private Authentication $auth;
  private RestApi $restApi;

  public function __construct(
    Authentication $authentication,
    GalleryRepository $galleryRepository,
    RestApi $restApi)
  {
    $this->galleryRepository = $galleryRepository;
    $this->auth = $authentication;
    $this->restApi = $restApi;
  }

  public function processRequest($requestMethod, $requestURI, $header, $body)
  {
    $uri = Utils::getRequestUri($requestURI);
    try {
      $response = $this->restApi->handleRequest($requestMethod, '/' . $uri, $header, $body);
      if ($response !== null) {
        http_response_code($response->getStatus());
        echo $response->getBody();
        exit;
      }
    } catch (ControllerDefinitionException $e) {
    }
  }

  public function getGalleryRepository(): GalleryRepository
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
