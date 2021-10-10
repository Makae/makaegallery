<?php
use ch\makae\makaegallery\App;
use ch\makae\makaegallery\ConversionConfig;
use ch\makae\makaegallery\GalleryLoader;
use ch\makae\makaegallery\GalleryRepository;
use ch\makae\makaegallery\ImageConverter;
use ch\makae\makaegallery\PartsLoader;
use ch\makae\makaegallery\PublicGallery;
use ch\makae\makaegallery\rest\RestApi;
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\security\Security;
use ch\makae\makaegallery\session\SessionProvider;
use ch\makae\makaegallery\UploadHandler;
use ch\makae\makaegallery\Utils;
use ch\makae\makaegallery\web\AuthenticationRestController;
use ch\makae\makaegallery\web\GalleryRestController;
use ch\makae\makaegallery\web\ImageRestController;

require_once('./loader.php');
load_dependencies(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
require_once('./config.php');
global $App;
$sessionProvider = new SessionProvider();
$security = new Security($sessionProvider);
$authentication = new Authentication($sessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS));
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

$restApi = new RestApi(WWW_BASE . '/api', $authentication);
$restApi->addController(new AuthenticationRestController($security, $authentication));
$restApi->addController(new GalleryRestController($galleryRepository, $security, new UploadHandler($galleryRepository)));
$restApi->addController(new ImageRestController($galleryRepository));

$App = new App(
  $sessionProvider,
  $security,
  $authentication,
  $galleryRepository,
  $restApi,
  new PartsLoader(PARTS_DIR, SUB_ROOT)
);

$App->processRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], Utils::getAllHeaders(), $_REQUEST);
