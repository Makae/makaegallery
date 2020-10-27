<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\GalleryConverter;
use PHPUnit\Framework\TestCase;

class GalleryConverterTest extends TestCase
{
    private static $folder = TEST_GALLERIES_FOLDER . DIRECTORY_SEPARATOR . 'testgallery';

    public function test_convertTo_withValidConfig_works()
    {
        $galleryConverter = new GalleryConverter(
            [
                "resized" => ConversionConfig::fromArray([
                    'w' => 800,
                    'h' => 800,
                    'q' => 80,
                    'm' => GalleryConverter::RESIZE_MODE_TO_SMALLER
                ]),
                "quality" => ConversionConfig::fromArray([
                    'q' => 75,
                    'm' => GalleryConverter::RESIZE_MODE_NO_RESIZE
                ])
            ]
        );
        $resultResized = $galleryConverter->convertTo("resized", static::$folder . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultResized->getWidth(), 800);
        $this->assertTrue(file_exists(static::$folder . DIRECTORY_SEPARATOR . 'resized-first-800-1200-80.jpg'));

        $resultOptimized = $galleryConverter->convertTo("quality", static::$folder . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultOptimized->getWidth(), 1080);
        $this->assertEquals($resultOptimized->getHeight(), 1620);
        $this->assertTrue(file_exists(static::$folder . DIRECTORY_SEPARATOR . 'resized-first-1080-1620-75.jpg'));
    }

    public function test_convertTo_withSpecialTargetpath_works()
    {
        $galleryConverter = new GalleryConverter(
            [
                "resized" => ConversionConfig::fromArray([
                    'w' => 800,
                    'h' => 800,
                    'q' => 80,
                    'm' => GalleryConverter::RESIZE_MODE_TO_SMALLER
                ]),
                "quality" => ConversionConfig::fromArray([
                    'q' => 75,
                    'm' => GalleryConverter::RESIZE_MODE_NO_RESIZE,
                    's' => 'optimized'
                ])
            ]
        );
        $resultResized = $galleryConverter->convertTo("resized", static::$folder . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultResized->getWidth(), 800);
        $this->assertTrue(file_exists(static::$folder . DIRECTORY_SEPARATOR . 'resized-first-800-1200-80.jpg'));

        $resultOptimized = $galleryConverter->convertTo("quality", static::$folder . DIRECTORY_SEPARATOR . 'first.jpg');
        $this->assertEquals($resultOptimized->getWidth(), 1080);
        $this->assertEquals($resultOptimized->getHeight(), 1620);
        $this->assertTrue(file_exists(static::$folder . DIRECTORY_SEPARATOR . 'optimized' . DIRECTORY_SEPARATOR . 'resized-first-1080-1620-75.jpg'));
    }
}
