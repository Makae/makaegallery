<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\GalleryConverter;
use ch\makae\makaegallery\Gallery;
use ch\makae\makaegallery\ConversionConfig;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    private static $folder = TEST_GALLERIES_FOLDER . DIRECTORY_SEPARATOR . 'testgallery';

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
        $images = $gallery->getImageList();
        $this->assertTrue(count($images) >= 3);
        $this->assertTrue(file_exists($images[0]['original_path']));
    }

    private function getGallery() {
        return new Gallery(GalleryTest::$folder, [
            'title' => 'testgallery',
            'description' => 'test description',
            'root_dir' => 'ROOT_DIR',
            'url_base' => 'URL_BASE',
            'level' => 0
        ]);
    }
}
