<?php

namespace ch\makae\makaegallery;

class PartsLoader
{
    private $ajax;
    private $partsDir;

    public function __construct($partsDirectory, $subRoot, AjaxRequestHandler $ajax)
    {
        $this->partsDir = $partsDirectory;
        $this->subRoot = $subRoot;
        $this->ajax = $ajax;
    }

    public function load($view, $requestURI)
    {
        $viewFile = $this->partsDir . DIRECTORY_SEPARATOR . $view . '.php';
        ob_start();
        if (!file_exists($viewFile)) {
            include_once($this->partsDir . DIRECTORY_SEPARATOR . '404.php');
        } else {
            include_once($viewFile);
        }

        $view_output = ob_get_clean();
        if (!$this->ajax->isAjaxRequest())
            include_once($this->partsDir . DIRECTORY_SEPARATOR . 'header.php');

        echo $view_output;

        if (!$this->ajax->isAjaxRequest())
            include_once($this->partsDir . DIRECTORY_SEPARATOR . 'footer.php');
    }

    public function pathFromURI($requestURI)
    {
        $subDir = $this->subRoot === '' ? '' : $this->subRoot . '\/';
        $regex = '/(https?:)?\/\/?' . $subDir .'([^\?]+)+\?.*/';
        return preg_replace($regex, '$2', $requestURI);
    }

}
