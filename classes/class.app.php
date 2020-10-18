<?php

namespace ch\makae\makaegallery;

class App
{
    const DEFAULT_VIEW = 'list';

    private $sessionProvider;
    private $makaeGallery;
    private $partsLoader;
    private $ajax;
    private $auth;

    public function __construct(
        ISessionProvider $sessionProvider,
        Authentication $authentication,
        MakaeGallery $makaeGallery,
        AjaxRequestHandler $ajax,
        PartsLoader $partsLoader)
    {
        $this->sessionProvider = $sessionProvider;
        $this->makaeGallery = $makaeGallery;
        $this->auth = $authentication;
        $this->ajax = $ajax;
        $this->partsLoader = $partsLoader;

        $this->sessionProvider->start();
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

    public function getMakaeGallery()
    {
        return $this->makaeGallery;
    }

    public function getAjax()
    {
        return $this->ajax;
    }

    public function getAuth()
    {
        return $this->auth;
    }
}
