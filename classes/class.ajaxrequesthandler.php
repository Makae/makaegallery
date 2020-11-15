<?php

namespace ch\makae\makaegallery;

class AjaxRequestHandler
{
    private GalleryRepository $galleryRepository;
    private bool $active;
    private UploadHandler $uploadHandler;
    private Security $security;

    public function __construct(GalleryRepository $galleryRepository, Security $security, UploadHandler $uploadHandler, $active = false)
    {
        $this->galleryRepository = $galleryRepository;
        $this->security = $security;
        $this->uploadHandler = $uploadHandler;
        $this->active = $active;
    }

    public function isAjaxRequest()
    {
        return $this->active;
    }

    public function admin()
    {
        call_user_func_array(array($this, 'admin_action_' . $_REQUEST['action']), $_REQUEST);
    }

    public function admin_action_upload_images($params)
    {
        $galleryId = isset($_REQUEST['galleryid']) ? $_REQUEST['galleryid'] : null;
        $nonceToken = isset($_REQUEST['nonce']) ? $_REQUEST['nonce'] : null;
        /* if (!$this->security->validateNonceToken($nonceToken)) {
             http_response_code(401);
             echo "Nonce token is invalid!";
             exit;
         }*/

        $files = $this->uploadHandler->getUploadedFiles($_FILES["images"]);
        $result = $this->uploadHandler->addUploadedImages(
            $galleryId,
            $files
        );
        if ($result->isSuccess()) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        echo json_encode(array(
            'msg' => 'Added Images to ' . $galleryId,
            'galleryid' => $galleryId,
            'result' => $result
        ));
        exit();
    }

    public function admin_action_clear_minified($params)
    {
        $gallery = isset($_REQUEST['galleryid']) ? $_REQUEST['galleryid'] : null;
        $this->galleryRepository->clearProcessedImages($gallery);
        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Gallery ' . $gallery . ' cleared',
            'galleryid' => $gallery
        ));
        exit();
    }

    public function admin_action_minify_image($params)
    {
        $imgid = urldecode($_REQUEST['imageid']);

        $image = $this->galleryRepository->processImageById($imgid);

        header('Content-Type: text/json');
        if (is_null($image)) {
            echo json_encode(array(
                'status' => 'error',
                'msg' => 'Could not find image'
            ));
            exit();
        }

        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Image was converted',
            'data' => Utils::mapImageToArray($image)
        ));
        exit();
    }
}
