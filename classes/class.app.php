<?php

namespace ch\makae\makaegallery;

class App
{
    const DEFAULT_VIEW = 'list';

    private ISessionProvider $sessionProvider;
    private GalleryRepository $galleryRepository;
    private PartsLoader $partsLoader;
    private AjaxRequestHandler $ajax;
    private Authentication $auth;
    private Security $security;

    public function __construct(
        ISessionProvider $sessionProvider,
        Security $security,
        Authentication $authentication,
        GalleryRepository $galleryRepository,
        AjaxRequestHandler $ajax,
        PartsLoader $partsLoader)
    {
        $this->security = $security;
        $this->sessionProvider = $sessionProvider;
        $this->galleryRepository = $galleryRepository;
        $this->auth = $authentication;
        $this->ajax = $ajax;
        $this->partsLoader = $partsLoader;

        $this->sessionProvider->start();
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function processRequest($requestURI, $getParams)
    {
        if (isset($getParams['logout'])) {
            $this->auth->logout();
        }

        $route = Utils::getUriComponents();
        $route[0] = isset($route[0]) ? $route[0] : self::DEFAULT_VIEW;
        $view = $route[0];
        $path = join('/', $route);
        if (!$this->auth->routeAllowed($path)) {
            $view = 'login';
        }

        $this->partsLoader->load($view, $requestURI);
    }

    public function getSessionProvider()
    {
        return $this->sessionProvider;
    }

    public function getGalleryRepository()
    {
        return $this->galleryRepository;
    }

    public function getAjax(): AjaxRequestHandler
    {
        return $this->ajax;
    }

    public function getAuth()
    {
        return $this->auth;
    }

}
