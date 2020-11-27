<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\rest\MultiRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\Route;
use ch\makae\makaegallery\rest\RouteDeclarations;

class GalleryRestController extends MultiRestController
{

    public function __construct()
    {
        parent::__construct(new RouteDeclarations([
                [new Route('/api/gallery/{gallery_id}'), [$this, 'getGalleryEndpoint']],
        ]));
    }

    public function getGalleryEndpoint(RequestData $requestData) {

    }


}
