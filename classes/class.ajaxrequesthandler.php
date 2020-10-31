<?php

namespace ch\makae\makaegallery;

class AjaxRequestHandler
{
    private $makaeGallery;
    private $active;

    public function __construct(GalleryRepository $makaeGallery, $active = false)
    {
        $this->active = $active;
        $this->makaeGallery = $makaeGallery;
    }

    public function isAjaxRequest()
    {
        return $this->active;
    }

    public function admin()
    {
        call_user_func_array(array($this, 'admin_action_' . $_REQUEST['action']), $_REQUEST);
    }

    public function admin_action_clear_minified($params)
    {
        $gallery = isset($_REQUEST['galleryid']) ? $_REQUEST['galleryid'] : null;
        $this->makaeGallery->clearProcessedImages($gallery);
        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Gallery ' . $gallery . ' cleared',
            'galleryid' => $gallery
        ));
        exit();
    }

    public function admin_action_update_image_list($params)
    {
        $gallery = isset($_REQUEST['galleryid']) ? $_REQUEST['galleryid'] : null;
        $diff = $this->makaeGallery->updateImageList($gallery);
        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Gallery ' . $gallery . ' updated',
            'galleryid' => $gallery,
            'diff' => $diff
        ));
        exit();
    }

    public function admin_action_minify_image($params)
    {
        $imgid = urldecode($_REQUEST['imageid']);

        $image = $this->makaeGallery->processImage($imgid);

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
