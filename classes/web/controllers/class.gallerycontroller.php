<?php


namespace ch\makae\makaegallery\web\controllers;

use ch\makae\makaegallery\IGalleryRepository;
use ch\makae\makaegallery\rest\controllers\MultiRestController;
use ch\makae\makaegallery\rest\GETRoute;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\POSTRoute;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\UploadHandler;
use ch\makae\makaegallery\web\DtoMapper;

class GalleryRestController extends MultiRestController
{
  private IGalleryRepository $galleryRepository;
  private UploadHandler $uploadHandler;

  public function __construct(IGalleryRepository $galleryRepository, UploadHandler $uploadHandler)
  {
    parent::__construct(new RouteDeclarations([
      [new GETRoute('/api/galleries/', Authentication::ACCESS_LEVEL_PUBLIC), [$this, 'getAllGalleries']],
      [new GETRoute('/api/galleries/{gallery_id}', Authentication::ACCESS_LEVEL_PUBLIC), [$this, 'getGallery']],
      [new GETRoute('/api/galleries/clear', Authentication::ACCESS_LEVEL_TENANT_ADMIN), [$this, 'clearAllGalleries']],
      [new GETRoute('/api/galleries/{gallery_id}/clear', Authentication::ACCESS_LEVEL_TENANT_ADMIN), [$this, 'clearGallery']],
      [new POSTRoute('/api/galleries/{gallery_id}/image', Authentication::ACCESS_LEVEL_TENANT_ADMIN), [$this, 'addImage']]
    ]));

    $this->galleryRepository = $galleryRepository;
    $this->uploadHandler = $uploadHandler;
  }

  public function addImage(RequestData $requestData): HttpResponse
  {
    $galleryId = $requestData->getParameter('gallery_id');
    $gallery = $this->galleryRepository->getGallery($galleryId);
    if (is_null($gallery)) {
      return HttpResponse::responseNotFound("Gallery with id `$galleryId` can't be found");
    }

    $files = $this->uploadHandler->getUploadedFiles($_FILES["images"]);
    $result = $this->uploadHandler->addUploadedImages(
      $galleryId,
      $files
    );
    if ($result->isSuccess()) {
      return HttpResponse::responseOK(json_encode(array(
        'msg' => 'Added Images to ' . $galleryId,
        'galleryId' => $galleryId,
        'result' => $result
      )));
    }
    return HttpResponse::responseServerError(false);

  }

  public function getAllGalleries(): HttpResponse
  {
    $galleries = $this->galleryRepository->getGalleries();

    return new HttpResponse(
      json_encode(DtoMapper::mapGalleryArrayToDto($galleries)),
      HttpResponse::STATUS_OK);
  }

  public function clearAllGalleries(): HttpResponse
  {
    $galleries = $this->galleryRepository->getGalleries();

    $galleriesWithIssues = [];
    foreach ($galleries as $gallery) {
      if (!$this->_clearGallery($gallery->getIdentifier())) {
        array_push($galleriesWithIssues, $gallery->getIdentifier());
      }
    }
    if (count($galleriesWithIssues)) {
      return HttpResponse::responseNotFound("Galleries with id `$galleriesWithIssues` can't be found");
    }
    return new HttpResponse(
      true,
      HttpResponse::STATUS_OK);
  }

  private function _clearGallery(string $galleryId): bool
  {
    $gallery = $this->galleryRepository->getGallery($galleryId);
    if (is_null($gallery)) {
      return false;
    }
    $gallery->clearProcessed();
    return true;
  }

  public function clearGallery(RequestData $requestData): HttpResponse
  {
    $galleryId = $requestData->getParameter('gallery_id');
    if ($this->_clearGallery($galleryId)) {
      return HttpResponse::responseNotFound("Gallery with id `$galleryId` can't be found");
    }

    return new HttpResponse(
      true,
      HttpResponse::STATUS_OK);
  }

  public function getGallery(RequestData $requestData): HttpResponse
  {
    $galleryId = $requestData->getParameter('gallery_id');
    $gallery = $this->galleryRepository->getGallery($galleryId);

    if (is_null($gallery)) {
      return HttpResponse::responseNotFound("Gallery with id `$galleryId` can't be found");
    }
    return new HttpResponse(
      json_encode(DtoMapper::mapGalleryToDto($gallery)),
      HttpResponse::STATUS_OK);
  }

}
