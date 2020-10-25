<?php
// JPG TESTS
use ch\makae\makaegallery\GalleryConverter;

$converter = new GalleryConverter();
$converter->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg',
    array(
        'w' => 450,
        'h' => null,
        'q' => 55
    ));

$converter->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg',
    array(
        'q' => 55
    ));

$converter->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg',
    array(
        'q' => 65
    ));
