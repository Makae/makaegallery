<?php
// JPG TESTS
Convert::instance()->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg', 
    array(
        'w' => 450,
        'h' => null,
        'q' => 55
    ));

    Convert::instance()->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg', 
    array(
        'q' => 55
    ));

    Convert::instance()->resize(
    TESTDIR . 'gallery' . DIRECTORY_SEPARATOR . 'test-1.jpg', 
    array(
        'q' => 65
    ));
