<?php

use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\ImageConverter;
use ch\makae\makaegallery\tests\GalleryHelper;
use PHPUnit\Framework\TestCase;

class ImageConverterTest extends TestCase
{

    public static function tearDownAfterClass(): void
    {
        GalleryHelper::cleanUp();
    }

    public function test_convertTo_withValidConfig_works()
    {
        $galleryConverter = GalleryHelper::getStandardConverter();
        $resultResized = $galleryConverter->convertTo("thumbnail", GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultResized->getWidth(), 800);
        $this->assertTrue(file_exists(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'converted' . DIRECTORY_SEPARATOR . 'resized-first-800-1200-80.jpg'));

        $resultOptimized = $galleryConverter->convertTo("optimized", GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultOptimized->getWidth(), 1080);
        $this->assertEquals($resultOptimized->getHeight(), 1620);
        $this->assertTrue(file_exists(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'converted' . DIRECTORY_SEPARATOR . 'resized-first-1080-1620-75.jpg'));
    }

    public function test_convertTo_withSpecialTargetpath_works()
    {
        $galleryConverter = new ImageConverter(
            [
                "resized" => ConversionConfig::fromArray([
                    'width' => 800,
                    'height' => 800,
                    'quality' => 80,
                    'mode' => ImageConverter::RESIZE_MODE_TO_SMALLER
                ]),
                "quality" => ConversionConfig::fromArray([
                    'quality' => 75,
                    'mode' => ImageConverter::RESIZE_MODE_NO_RESIZE,
                    'subDir' => 'optimized'
                ])
            ]
        );
        $resultResized = $galleryConverter->convertTo("resized", GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultResized->getWidth(), 800);
        $this->assertTrue(file_exists(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'converted' . DIRECTORY_SEPARATOR . 'resized-first-800-1200-80.jpg'));

        $resultOptimized = $galleryConverter->convertTo("quality", GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultOptimized->getWidth(), 1080);
        $this->assertEquals($resultOptimized->getHeight(), 1620);
        $this->assertTrue(file_exists(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'optimized' . DIRECTORY_SEPARATOR . 'resized-first-1080-1620-75.jpg'));
    }
}
