<?php

class AJAX {
    public function admin() {
        call_user_func_array(array('AJAX', 'admin_action_' . $_REQUEST['action']), $_REQUEST);
    }

    public static function admin_action_clear_minified($params) {
        Utils::clearMinifiedImages();
    }
    public static function admin_action_minify_image($params) {
        $imgid = urldecode($_REQUEST['imageid']);
        list($gallery_id, $photo_id) = explode('|', $imgid);

        $gallery = Utils::getGallery($gallery_id);
        $img = $gallery->getImage($imgid);
        $img = $gallery->processImage($img);
        unset($img['original_path']);
        header('Content-Type: text/json');
        if(is_null($img)) {
            echo json_encode(array(
                'status' => 'error',
                'msg' => 'Could not find image'
            ));
            exit();
        }

        echo json_encode(array(
            'status' => 'success',
            'msg' => 'Image was converted',
            'data' => $img
        ));
        exit();
    }
}