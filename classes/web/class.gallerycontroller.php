<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\MultiRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\Route;
use ch\makae\makaegallery\rest\RouteDeclarations;

class GalleryRestController extends MultiRestController
{
    private GalleryRepository $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository)
    {
        parent::__construct(new RouteDeclarations([
            [new Route('/api/gallery/{gallery_id}'), [$this, 'getGallery']],
        ]));

        $this->galleryRepository = $galleryRepository;
    }

    public function getGallery(RequestData $requestData): HttpResponse
    {
        $gallery_id = $requestData->getParameter('gallery_id');
        $gallery = $this->galleryRepository->getGallery($gallery_id);
        if(is_null($gallery)) {
            return HttpResponse::responseNotFound("Gallery with id `$gallery_id` can't be found");
        }
        return new HttpResponse(
            json_encode(DtoMapper::mapGalleryToDto($gallery)),
            HttpResponse::STATUS_OK);
    }

}
