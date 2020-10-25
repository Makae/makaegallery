<?php
require_once('../loader.php');

load_test_dependencies('../');

use ch\makae\makaegallery\GalleryConverter;
use ch\makae\makaegallery\GalleryLoader;
use ch\makae\makaegallery\ConversionConfig;
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
