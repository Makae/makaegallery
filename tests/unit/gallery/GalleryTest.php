<?php

use ch\makae\makaegallery\tests\GalleryHelper;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    public function test_createGallery_works()
    {
        $gallery = GalleryHelper::getGallery();
        $this->assertEquals($gallery->getTitle(), 'testgallery');
        $this->assertEquals($gallery->getDescription(), 'test description');
        $this->assertEquals($gallery->getLevel(), 0);
    }

    public function test_gettingImages_processesImages()
    {
        $gallery = GalleryHelper::getGallery();
        $images = $gallery->getImages();
        $this->assertTrue(count($images) >= 2);
        $this->assertTrue(file_exists($images[0]->getSource()));
        $this->assertNull($images[0]->getThumbnailUrl());
        $this->assertNull($images[0]->getOptimizedUrl());
    }
}
