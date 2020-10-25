<?php
namespace ch\makae\makaegallery;

class GalleryConverter {
    const MODE_TO_SMALLER = 'to_smaller';
    const MODE_WIDTH_OR_HEIGHT = 'worh';

    private $configs = [];

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    public function convertTo($configKey, $filepath, $targetpath=null, $skip_existing=true) {
        if(!array_key_exists($configKey, $this->configs)) {
            throw new \Exception("Can not find $configKey in processors.");
        }
        $this->resize($filepath, $this->configs[$configKey], $targetpath, $skip_existing);
    }

    // Only resizable to width (at the moment)
    public function resize($filepath, ConversionConfig $config, $targetpath=null, $skip_existing=true) {
        list($o_width, $o_height) = getImageSize($filepath);
        
        $to_width = false;

        if($config->getResizeMode() == GalleryConverter::MODE_TO_SMALLER) {
            $to_width = $o_width < $o_height;
        } else if ($config->getResizeMode() == GalleryConverter::MODE_WIDTH_OR_HEIGHT) {
            $to_width = $config->getWidth() ? true : false;
        }
        
        if($to_width) {
            $factor = $config->getWidth() / $o_width;
        } else if(!$to_width && $config->getHeight()) {
            $factor = $config->getHeight() / $o_height;
        } else {
            $factor = 1;
        }

        $n_width  = round($o_width * $factor);
        $n_height = round($o_height * $factor);

        $sep = DIRECTORY_SEPARATOR == '\\' ? '\\\\' : '/';
        if(is_null($targetpath)) {
            $tmp = str_replace('\\', '/', $filepath);
            $targetpath = preg_replace("/(.*)\/([^\/]+)(\.[^\.]+)$/i", "$1" . $sep . "{prefix}$2-{width}-{height}-{quality}$3", $tmp);
            $targetpath = str_replace('/', DIRECTORY_SEPARATOR, $targetpath);
        }
        $targetpath = str_replace('{prefix}',  $config->getPrefix(), $targetpath);
        $targetpath = str_replace('{width}',   $n_width, $targetpath);
        $targetpath = str_replace('{height}',  $n_height, $targetpath);
        $targetpath = str_replace('{quality}', $config->getQuality(), $targetpath);

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


        $this->saveImage($n_img, $targetpath, $config['q']);
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
