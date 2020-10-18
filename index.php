<?php

use ch\makae\makaegallery\AjaxRequestHandler;
use ch\makae\makaegallery\App;
use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\Converter;
use ch\makae\makaegallery\MakaeGallery;
use ch\makae\makaegallery\PartsLoader;
use ch\makae\makaegallery\Processor;
use ch\makae\makaegallery\SessionProvider;

require_once('./loader.php');
load_dependencies();
require_once('./config.php');

global $App;
$sessionProvider = new SessionProvider();
$converter = new Converter();
$makaeGallery = new MakaeGallery(
    GALLERY_ROOT,
    unserialize(GALLERY_CONFIGURATION),
    new Processor($converter, "optimized", unserialize(PROCESS_CONFIG_NORMAL)),
    new Processor($converter, "thumb", unserialize(PROCESS_CONFIG_THUMB))
);
$ajax = new AjaxRequestHandler($makaeGallery, DOING_AJAX);
$App = new App(
    $sessionProvider,
    new Authentication($sessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS)),
    $makaeGallery,
    $ajax,
    new PartsLoader(PARTS_DIR, SUB_ROOT, $ajax));

$App->processRequest($_SERVER['REQUEST_URI'], $_GET);

