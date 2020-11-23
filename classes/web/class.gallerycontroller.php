<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\RestController;

class GalleryController extends RestController
{

    public function __construct()
    {
        parent::__construct('/api/gallery/{gallery_id}');
    }

    public function handle(string $path, array $header, array $body): HttpResponse
    {
        $requestData = $this->getRequestData($path, $header, $body);
        return new HttpResponse(json_encode($requestData->getParameters()));
    }

}
