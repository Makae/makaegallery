<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\Gallery;
use ch\makae\makaegallery\ImageConverter;
use ch\makae\makaegallery\PublicGallery;
use ch\makae\makaegallery\Utils;

class GalleryHelper
{
    const TEST_GALLERY_FOLDER = TEST_GALLERIES_FOLDER . DIRECTORY_SEPARATOR . 'testgallery';

    public static function cleanUp()
    {
        Utils::rmdir(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'optimized');
        Utils::rmfile(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'resized-first-800-1200-80.jpg');
        Utils::rmfile(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'resized-first-1080-1620-75.jpg');
    }

    public static function getPublicGallery()
    {
        return new PublicGallery(GalleryHelper::getGallery(), GalleryHelper::getStandardConverter(), BASE_PATH, BASE_URL);
    }

    public static function getGallery()
    {
        return Gallery::fromArray(GalleryHelper::TEST_GALLERY_FOLDER, [
            'title' => 'testgallery',
            'description' => 'test description',
            'root_dir' => 'ROOT_DIR',
            'url_base' => 'URL_BASE',
            'level' => 0
        ]);
    }

    public static function getStandardConverter()
    {
        return new ImageConverter(
            [
                "thumbnail" => ConversionConfig::fromArray([
                    'width' => 800,
                    'height' => 800,
                    'quality' => 80,
                    'mode' => ImageConverter::RESIZE_MODE_TO_SMALLER
                ]),
                "optimized" => ConversionConfig::fromArray([
                    'quality' => 75,
                    'mode' => ImageConverter::RESIZE_MODE_NO_RESIZE
                ])
            ]
        );
    }
}
