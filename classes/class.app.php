<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\rest\ControllerDefinitionException;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\security\Security;
use ch\makae\makaegallery\session\ISessionProvider;

class App
{
    const DEFAULT_VIEW = 'list';

    private ISessionProvider $sessionProvider;
    private GalleryRepository $galleryRepository;
    private PartsLoader $partsLoader;
    private Authentication $auth;
    private Security $security;
    private RestApi $restApi;

    public function __construct(
        ISessionProvider $sessionProvider,
        Security $security,
        Authentication $authentication,
        GalleryRepository $galleryRepository,
        RestApi $restApi,
        PartsLoader $partsLoader)
    {
        $this->security = $security;
        $this->sessionProvider = $sessionProvider;
        $this->galleryRepository = $galleryRepository;
        $this->auth = $authentication;
        $this->restApi = $restApi;
        $this->partsLoader = $partsLoader;

        $this->sessionProvider->start();
    }

    public function getSecurity(): Security
    {
        return $this->security;
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

        if (isset($getParams['logout'])) {
            $this->auth->logout();
        }

        $route = Utils::getUriComponents($uri);
        $route[0] = isset($route[0]) ? $route[0] : self::DEFAULT_VIEW;
        $view = $route[0];
        $path = join('/', $route);
        if (!$this->auth->routeAllowed($path)) {
            $view = 'login';
        }

        $this->partsLoader->load($view, $requestURI);
    }

    public function getSessionProvider(): ISessionProvider
    {
        return $this->sessionProvider;
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

    public function setRestApi(RestApi $restApi): void
    {
        $this->restApi = $restApi;
    }

}
