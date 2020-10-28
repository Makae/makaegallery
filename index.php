<?php

use ch\makae\makaegallery\AjaxRequestHandler;
use ch\makae\makaegallery\App;
use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\ImageConverter;
use ch\makae\makaegallery\GalleryLoader;
use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\PartsLoader;
use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\SessionProvider;

require_once('./loader.php');
load_dependencies();
require_once('./config.php');

global $App;
$sessionProvider = new SessionProvider();
$galleryLoader = new GalleryLoader(GALLERY_ROOT,
    unserialize(GALLERY_CONFIGURATION)
);
$galleryConverter = new ImageConverter(
    [
        "optimized" => ConversionConfig::fromArray(unserialize(PROCESS_CONFIG_NORMAL)),
        "thumb" => ConversionConfig::fromArray(unserialize(PROCESS_CONFIG_THUMB))
    ]
);
$makaeGallery = new GalleryRepository(
    $galleryLoader,
    $galleryConverter
);
$ajax = new AjaxRequestHandler($makaeGallery, DOING_AJAX);
$App = new App(
    $sessionProvider,
    new Authentication($sessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS)),
    $makaeGallery,
    $ajax,
    new PartsLoader(PARTS_DIR, SUB_ROOT, $ajax));

$App->processRequest($_SERVER['REQUEST_URI'], $_GET);

