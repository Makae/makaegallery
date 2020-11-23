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
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\Security;
use ch\makae\makaegallery\session\SessionProvider;
use ch\makae\makaegallery\UploadHandler;
use ch\makae\makaegallery\Utils;
use ch\makae\makaegallery\web\GalleryController;

require_once('./loader.php');
load_dependencies(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
require_once('./config.php');

global $App;
$sessionProvider = new SessionProvider();
$security = new Security($sessionProvider);
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
$galleryRepository = new GalleryRepository(
    $galleryLoader,
    $galleryConverter
);
$ajax = new AjaxRequestHandler(
    $galleryRepository,
    $security,
    new UploadHandler($galleryRepository),
    DOING_AJAX
);

$restApi = new RestApi();
$restApi->addController(new GalleryController());

$App = new App(
    $sessionProvider,
    $security,
    new Authentication($sessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS)),
    $galleryRepository,
    $restApi,
    new PartsLoader(PARTS_DIR, SUB_ROOT, $ajax)
);

$App->processRequest($_SERVER['REQUEST_URI'], Utils::getAllHeaders(), $_REQUEST);

