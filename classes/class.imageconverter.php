<?php

namespace ch\makae\makaegallery;

class ImageConverter
{
    const RESIZE_MODE_NO_RESIZE = 'no_resize';
    const RESIZE_MODE_TO_SMALLER = 'to_smaller';
    const RESIZE_MODE_TO_DEFINED_DIMENSION = 'to_defined_dimension';

    private array $configs;

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    public function convertTo($configKey, $filepath, $skip_existing = true): ConversionResult
    {
        if (!array_key_exists($configKey, $this->configs)) {
            throw new \Exception("Can not find $configKey in processors.");
        }
        return $this->resize($filepath, $this->configs[$configKey], $skip_existing);
    }

    public function resize($filepath, ConversionConfig $config, $skip_existing = true): ConversionResult
    {
        list($original_width, $original_height) = getImageSize($filepath);

        $factor = $this->getScaleFactor($config, $original_width, $original_height);

        $new_width = round($original_width * $factor);
        $new_height = round($original_height * $factor);

        $targetpath = $this->getTargetPath($filepath, $config, $new_width, $new_height);

        if ($skip_existing && file_exists($targetpath)) {
            return new ConversionResult(
                $filepath,
                $targetpath,
                $new_width,
                $new_height
            );
        }

        static::saveResizedImage($filepath, $targetpath, $original_width, $original_height, $new_width, $new_height, $config->getQuality());
        return new ConversionResult(
            $filepath,
            $targetpath,
            $new_width,
            $new_height
        );
    }

    private function getScaleFactor(ConversionConfig $config, $width, $height)
    {
        $to_width = false;
        if ($config->getResizeMode() == ImageConverter::RESIZE_MODE_NO_RESIZE) {
            return 1;
        } else if ($config->getResizeMode() == ImageConverter::RESIZE_MODE_TO_SMALLER) {
            $to_width = $width < $height;
        } else if ($config->getResizeMode() == ImageConverter::RESIZE_MODE_TO_DEFINED_DIMENSION) {
            $to_width = $config->hasWidth();
        }

        if ($to_width) {
            $factor = $config->getWidth() / $width;
        } else if (!$to_width && $config->getHeight()) {
            $factor = $config->getHeight() / $height;
        } else {
            $factor = 1;
        }
        return $factor;
    }

    private function getTargetPath(string $filepath, ConversionConfig $config, float $new_width, float $new_height)
    {
        $tmp = str_replace('\\', '/', $filepath);
        $targetpath = preg_replace("/(.*)\/([^\/]+)(\.[^\.]+)$/i", "$1{subdir}/{prefix}$2-{width}-{height}-{quality}$3", $tmp);
        $targetpath = str_replace('/', DIRECTORY_SEPARATOR, $targetpath);
        $targetpath = str_replace('{subdir}', $config->hasSubDir() ? DIRECTORY_SEPARATOR . $config->getSubDir() : '', $targetpath);
        $targetpath = str_replace('{prefix}', $config->getPrefix(), $targetpath);
        $targetpath = str_replace('{width}', $new_width, $targetpath);
        $targetpath = str_replace('{height}', $new_height, $targetpath);
        $targetpath = str_replace('{quality}', $config->getQuality(), $targetpath);

        if ($new_targetpath = preg_replace("/(.*)(\.[^\.]+)$/i", "$1.jpg", $targetpath)) {
            $targetpath = $new_targetpath;
        } else {
            $targetpath .= '.jpg';
        }

        return $targetpath;
    }

    private static function saveResizedImage(string $filepath, string $targetpath, int $original_width, int $original_height, int $new_width, int $new_height, int $quality)
    {
        $dir = dirname($targetpath);
        if(strpos($dir, dirname($filepath)) !== 0) {
            var_dump($dir);
            var_dump($filepath);
            die(var_dump(strpos($dir, dirname($filepath))));
            throw new \Exception("Targetpath: $targetpath is not in a (sub) folder of filepath-folder: $dir");
        }
        if(!file_exists($dir)) {
            mkdir($dir);
        }
        $new_img =& static::loadResizedImage($filepath, $original_width, $original_height, $new_width, $new_height);
        static::saveImage($new_img, $targetpath, $quality);
        imagedestroy($new_img);
    }

    private static function &loadResizedImage(string $filepath, int $original_width, int $original_height, int $width, int $height)
    {
        $new_img = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $new_img,
            static::loadImage($filepath),
            0, 0, 0, 0,
            $width,
            $height,
            $original_width,
            $original_height);
        return $new_img;
    }

    public static function loadImage($filepath)
    {
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
        $allowedTypes = [
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
            6   // [] bmp
        ];
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

    public static function saveImage($img, $target, $quality)
    {
        imagejpeg($img, $target, $quality);
    }
}
