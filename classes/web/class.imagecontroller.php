<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\MultiRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\Route;
use ch\makae\makaegallery\rest\RouteDeclarations;

class ImageRestController extends MultiRestController
{
    private GalleryRepository $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository)
    {
        parent::__construct(new RouteDeclarations([
            [new Route('/api/image/{image_id}/minify'), [$this, 'getMinifyImage']],
        ]));

        $this->galleryRepository = $galleryRepository;
    }

    public function getMinifyImage(RequestData $requestData): HttpResponse
    {
        $image_id = $requestData->getParameter('image_id');
        $gallery = $this->galleryRepository->getGallery($image_id);
        var_dump($gallery);
        return new HttpResponse(
            json_encode(DtoMapper::mapGalleryToDto($gallery)),
            HttpResponse::STATUS_OK);
    }

}
