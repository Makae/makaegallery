<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\tests\GalleryHelper;
use ch\makae\makaegallery\Utils;
use PHPUnit\Framework\TestCase;

class PublicGalleryTest extends TestCase
{
    public function tearDown()
    {
        Utils::rmdir(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'converted');
        Utils::rmfile(GalleryHelper::TEST_GALLERY_FOLDER . DIRECTORY_SEPARATOR . 'gallery.cache');
    }

    public function test_createPublicGallery_works()
    {
        $gallery = GalleryHelper::getPublicGallery();
        $this->assertEquals($gallery->getTitle(), 'testgallery');
        $this->assertEquals($gallery->getDescription(), 'test description');
        $this->assertEquals($gallery->getLevel(), 0);
    }

    public function test_gettingImages_processesImages()
    {
        $gallery = GalleryHelper::getPublicGallery();
        $image = $gallery->getImage('testgallery|first.jpg');
        $this->assertTrue(file_exists($image->getSource()));
        $this->assertEquals($image->getThumbnail(), 'https://makae.ch/tests/tests/galleries/testgallery/converted/resized-first-800-1200-80.jpg');
        $this->assertEquals($image->getImage(), 'https://makae.ch/tests/tests/galleries/testgallery/converted/resized-first-1080-1620-75.jpg');
    }

    public function test_gettingCachedImages_returnsFromCache()
    {
        $gallery = GalleryHelper::getPublicGallery();
        $image = $gallery->getImage('testgallery|first.jpg');
        $this->assertTrue(file_exists($image->getSource()));

        $gallery = GalleryHelper::getPublicGallery();
        $image = $gallery->getImage('testgallery|first.jpg');
        $this->assertTrue(file_exists($image->getSource()));
        $this->assertEquals($image->getThumbnail(), 'https://makae.ch/tests/tests/galleries/testgallery/converted/resized-first-800-1200-80.jpg');
        $this->assertEquals($image->getImage(), 'https://makae.ch/tests/tests/galleries/testgallery/converted/resized-first-1080-1620-75.jpg');

    }

    private function getGallery()
    {
        return Gallery::fromArray(GalleryTest::$folder, [
            'title' => 'testgallery',
            'description' => 'test description',
            'root_dir' => 'ROOT_DIR',
            'url_base' => 'URL_BASE',
            'level' => 0
        ]);
    }
}
