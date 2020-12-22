<?php

use ch\makae\makaegallery\GalleryLoader;
use PHPUnit\Framework\TestCase;

class GalleryLoaderTest extends TestCase
{
    private static $folder = TEST_GALLERIES_FOLDER;

    public function test_createGalleryLoader_works()
    {
        $galleryLoader = new GalleryLoader(static::$folder, [
                'testgallery' => array(
                    'title' => 'test gallery',
                    'description' => 'test description',
                    'level' => 2
                )
            ]
        );
        $galleries = $galleryLoader->loadGalleries();
        $this->assertTrue(count($galleries) === 1);
    }
}
