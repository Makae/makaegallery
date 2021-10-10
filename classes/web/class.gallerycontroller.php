<?php


namespace ch\makae\makaegallery\web;

use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\rest\GETRoute;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\MultiRestController;
use ch\makae\makaegallery\rest\POSTRoute;
use ch\makae\makaegallery\rest\RequestData;
use ch\makae\makaegallery\rest\RouteDeclarations;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\security\Security;
use ch\makae\makaegallery\UploadHandler;

class GalleryRestController extends MultiRestController
{
    private GalleryRepository $galleryRepository;
    private UploadHandler $uploadHandler;
    private Security $security;

    public function __construct(GalleryRepository $galleryRepository, Security $security, UploadHandler $uploadHandler)
    {
        parent::__construct(new RouteDeclarations([
            [new GETRoute('/api/gallery/clear', Authentication::ACCESS_LEVEL_USER), [$this, 'clearAllGalleries']],
            [new GETRoute('/api/gallery/{gallery_id}', Authentication::ACCESS_LEVEL_USER), [$this, 'getGallery']],
            [new GETRoute('/api/gallery/{gallery_id}/clear', Authentication::ACCESS_LEVEL_ADMIN), [$this, 'clearGallery']],
            [new POSTRoute('/api/gallery/{gallery_id}/image', Authentication::ACCESS_LEVEL_ADMIN), [$this, 'addImage']]
        ]));

        $this->galleryRepository = $galleryRepository;
        $this->security = $security;
        $this->uploadHandler = $uploadHandler;
    }

    public function addImage(RequestData $requestData): HttpResponse
    {
        $galleryId = $requestData->getParameter('gallery_id');
        $gallery = $this->galleryRepository->getGallery($galleryId);
        if (is_null($gallery)) {
            return HttpResponse::responseNotFound("Gallery with id `$galleryId` can't be found");
        }
        /*$nonceToken = $requestData->getParameter('nonce');
        if (!$this->security->validateNonceToken($nonceToken)) {
            return HttpResponse::responseUnauthorized("Nonce token is invalid!");
        }*/

        $files = $this->uploadHandler->getUploadedFiles($_FILES["images"]);
        $result = $this->uploadHandler->addUploadedImages(
            $galleryId,
            $files
        );
        if ($result->isSuccess()) {
            return HttpResponse::responseOK(json_encode(array(
                'msg' => 'Added Images to ' . $galleryId,
                'galleryid' => $galleryId,
                'result' => $result
            )));
        }
        return HttpResponse::responseServerError(false);

    }

    public function clearAllGalleries(RequestData $requestData): HttpResponse {
        $galleries = $this->galleryRepository->getGalleries();

        $galleriesWithIssues = [];
        foreach($galleries as $gallery) {
            if (!$this->_clearGallery($gallery->getIdentifier())) {
                array_push($galleriesWithIssues, $gallery->getIdentifier());
            }
        }
        if(count($galleriesWithIssues)) {
            return HttpResponse::responseNotFound("Galleries with id `$galleriesWithIssues` can't be found");
        }
        return new HttpResponse(
            true,
            HttpResponse::STATUS_OK);
    }

    private function _clearGallery(string $galleryId): bool {
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
