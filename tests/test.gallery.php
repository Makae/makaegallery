<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\Converter;
use ch\makae\makaegallery\Gallery;
use ch\makae\makaegallery\Processor;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    private static $folder = TEST_GALLERIES_FOLDER . DIRECTORY_SEPARATOR . 'testgallery';
    private static $optimizer;
    private static $thumbnailer;

    public static function setUpBeforeClass()
    {
        $converter = new Converter();
        GalleryTest::$optimizer = new Processor($converter, "optimized", [
            'q' => 80,
            'm' => 'none']);
        GalleryTest::$thumbnailer = new Processor($converter, "thumb", [
            'w' => 800,
            'h' => 800,
            'q' => 80,
            'm' => 'tosmaller']);
    }

    public function test_createGallery_works()
    {
        $gallery = $this->getGallery();
        $this->assertEquals($gallery->getTitle(), 'testgallery');
        $this->assertEquals($gallery->getDescription(), 'test description');
        $this->assertEquals($gallery->getLevel(), 0);
    }

    public function test_gettingImages_processesImages()
    {
        $gallery = $this->getGallery();
        $images = $gallery->getImageList(true, true);
        $this->assertTrue(file_exists($gallery->getResizeFolder()));
        $this->assertTrue(file_exists($gallery->getResizeFolder()));
    }

    private function getGallery() {
        return new Gallery(GalleryTest::$folder, [
            'title' => 'testgallery',
            'description' => 'test description',
            'root_dir' => 'ROOT_DIR',
            'url_base' => 'URL_BASE',
            'level' => 0
        ], GalleryTest::$optimizer, GalleryTest::$thumbnailer);
    }
}
