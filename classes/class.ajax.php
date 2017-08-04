<?php

class AJAX {

    public function admin() {
        call_user_func_array(array('AJAX', 'admin_action_' . $_REQUEST['action']), $_REQUEST);
    }

    public static function admin_action_clear_minified($params) {
        $gallery = isset($_REQUEST['galleryid']) ? $_REQUEST['galleryid'] : null;
        Utils::clearMinifiedImages($gallery);
        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Gallery ' . $gallery . ' cleared',
            'galleryid' => $gallery
        ));
        exit();
    }

    public static function admin_action_minify_image($params) {
        $imgid = urldecode($_REQUEST['imageid']);
        list($gallery_id, $photo_id) = explode('|', $imgid);

        $gallery = Utils::getGallery($gallery_id);
        $image = $gallery->getImage($imgid);
        $image = $gallery->processImage($image);
        $image = $gallery->addImageMeta($image);
        $gallery->setImageData($image, true);

        unset($image['original_path']);

        header('Content-Type: text/json');
        if(is_null($image)) {
            echo json_encode(array(
                'status' => 'error',
                'msg' => 'Could not find image'
            ));
            exit();
        }

        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Image was converted',
            'data' => $image
        ));
        exit();
    }
}