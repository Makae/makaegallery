<?php
namespace ch\makae\makaegallery;

class Converter {
    const MODE_TO_WIDTH = 'width';
    const CONVERT_DEFAULT_PREFIX = 'resized';

    public function optimizeImages($filepaths, $width, $quality) {
        $list = array();
        foreach($filepaths as $filepath) {
            $list[] = $this->resize($filepath, array());
        }
        return $list;
    }

    // Only resizable to with (at the moment)
    public function resize($filepath, $resize, $targetpath=null, $skip_existing=true) {
        $resize['prefix'] = isset($resize['prefix']) ? $resize['prefix'] : static::CONVERT_DEFAULT_PREFIX;
        $resize['w'] = isset($resize['w']) ? $resize['w'] : null;
        $resize['h'] = isset($resize['h']) ? $resize['h'] : null;
        $resize['q'] = isset($resize['q']) ? $resize['q'] : 80;
        $resize['m'] = isset($resize['m']) ? $resize['m'] : 'worh';
        $resize['q'] = min(max($resize['q'], 10), 1000);

        list($o_width, $o_height) = getImageSize($filepath);
        
        $to_width = false;

        if($resize['m'] == 'to_smaller') {
            $to_width = ($o_width < $o_height) ? true : false;
        } else if ($resize['m'] == 'worh') {
            $to_width = $resize['w'] ? true : false;
        }
        
        if($to_width) {
            $factor = $resize['w'] / $o_width;
        } else if(!$to_width && $resize['h']) {
            $factor = $resize['h'] / $o_height;
        } else {
            $factor = 1;
        }

        $n_width  = round($o_width * $factor);
        $n_height = round($o_height * $factor);

        $sep = DIRECTORY_SEPARATOR == '\\' ? '\\\\' : '/';
        if(is_null($targetpath)) {
            $tmp = str_replace('\\', '/', $filepath);
            $targetpath = preg_replace("/(.*)\/([^\/]+)(\.[^\.]+)$/i", "$1{prefix}" . $sep . "$2-{width}-{height}-{quality}$3", $tmp);
            $targetpath = str_replace('/', DIRECTORY_SEPARATOR, $targetpath);
        }
        $targetpath = str_replace('{prefix}',  DIRECTORY_SEPARATOR . $resize['prefix'], $targetpath);
        $targetpath = str_replace('{width}',   $n_width, $targetpath);
        $targetpath = str_replace('{height}',  $n_width, $targetpath);
        $targetpath = str_replace('{quality}', $resize['q'], $targetpath);

        if($n_targetpath = preg_replace("/(.*)(\.[^\.]+)$/i", "$1.jpg", $targetpath)) {
            $targetpath = $n_targetpath;
        } else {
            $targetpath .= '.jpg';
        }
        
        if($skip_existing && file_exists($targetpath)) {
            return array(
                $filepath,
                $targetpath,
                $n_width,
                $n_height
            );
        }

        $o_img = static::loadImage($filepath);
        $n_img = imagecreatetruecolor($n_width, $n_height);
        imagecopyresampled(
            $n_img, 
            $o_img,
            0,0,0,0,
            $n_width, 
            $n_height, 
            $o_width, 
            $o_height);


        $this->saveImage($n_img, $targetpath, $resize['q']);
        imagedestroy($n_img);
        return array(
            $filepath,
            $targetpath,
            $n_width,
            $n_height
        );
    }

    public function loadImage($filepath) {
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
        $allowedTypes = array( 
            1,  // [] gif 
            2,  // [] jpg 
            3,  // [] png 
            6   // [] bmp 
        ); 
        if (!in_array($type, $allowedTypes)) { 
            return false; 
        } 
        switch ($type) { 
            case 1 : 
                $im = imageCreateFromGif($filepath); 
            break; 
            case 2 : 
                $im = imageCreateFromJpeg($filepath); 
            break; 
            case 3 : 
                $im = imageCreateFromPng($filepath); 
            break; 
            case 6 : 
                $im = imageCreateFromBmp($filepath); 
            break; 
        }    
        return $im;  
    }

    public function saveImage($img, $target, $quality) {
        imagejpeg($img, $target, $quality);
    }
}
