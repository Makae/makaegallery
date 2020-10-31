<?php

use ch\makae\makaegallery\AjaxRequestHandler;
use ch\makae\makaegallery\App;
use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\GalleryLoader;
use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\ImageConverter;
use ch\makae\makaegallery\PartsLoader;
use ch\makae\makaegallery\PublicGallery;
use ch\makae\makaegallery\SessionProvider;

require_once('./loader.php');
load_dependencies();
require_once('./config.php');

global $App;
$sessionProvider = new SessionProvider();
$galleryLoader = new GalleryLoader(GALLERY_ROOT,
    unserialize(GALLERY_CONFIGURATION),
    GALLERY_DEFAULT_COVER
);
$galleryConverter = new ImageConverter(
    [
        PublicGallery::PROCESSOR_OPTIMIZED_KEY => ConversionConfig::fromArray(unserialize(PROCESS_CONFIG_NORMAL)),
        PublicGallery::PROCESSOR_THUMBNAIL_KEY => ConversionConfig::fromArray(unserialize(PROCESS_CONFIG_THUMB))
    ]
);
$makaeGallery = new GalleryRepository(
    $galleryLoader,
    $galleryConverter
);
$ajax = new AjaxRequestHandler($makaeGallery, $sessionProvider, DOING_AJAX);
$App = new App(
    $sessionProvider,
    new Authentication($sessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS)),
    $makaeGallery,
    $ajax,
    new PartsLoader(PARTS_DIR, SUB_ROOT, $ajax));

$App->processRequest($_SERVER['REQUEST_URI'], $_GET);

