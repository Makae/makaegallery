<?php


namespace ch\makae\makaegallery\web\controllers;

use ch\makae\makaegallery\IGalleryRepository;
use ch\makae\makaegallery\rest\GETRoute;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\controllers\MultiRestController;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\web\DtoMapper;

class ImageRestController extends MultiRestController
{
  private IGalleryRepository $galleryRepository;

  public function __construct(IGalleryRepository $galleryRepository)
  {
    parent::__construct(new RouteDeclarations([
      [new GETRoute('/api/image/{image_id}/minify', Authentication::ACCESS_LEVEL_ADMIN), [$this, 'minifyImage']],
    ]));

    $this->galleryRepository = $galleryRepository;
  }

  public function minifyImage(RequestData $requestData): HttpResponse
  {
    $image_id = $requestData->getParameter('image_id');
    $image = $this->galleryRepository->processImageById($image_id);
    return new HttpResponse(
      json_encode(DtoMapper::mapImageToDto($image)),
      HttpResponse::STATUS_OK);
  }

}
