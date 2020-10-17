<?php

use ch\makae\makaegallery\AJAX;
use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\MakaeGallery;
use ch\makae\makaegallery\Utils;


require_once('./config.php');
require_once('./loader.php');

load_dependencies();

/*include_once('./tests/tests.php');*/
global $SessionProvider;
global $MakaeGallery;
global $AJAX;
global $Authentication;
$SessionProvider = new \ch\makae\makaegallery\SessionProvider();
$MakaeGallery = new MakaeGallery(
    GALLERY_ROOT,
    unserialize(GALLERY_CONFIGURATION)
);
$AJAX = new AJAX($MakaeGallery);
$Authentication = new Authentication($SessionProvider, SALT, unserialize(AUTH_USERS), unserialize(AUTH_RESTRICTIONS));

if (isset($_GET['logout'])) {
    $Authentication->logout();
}

$route = Utils::getUriComponents();
$view = isset($route[0]) ? $route[0] : 'list';

if (!$Authentication->urlAllowed($_SERVER['REQUEST_URI'])) {
    $redirect = $_SERVER['REQUEST_URI'];
    $view = 'login';
}


ob_start();
if (!file_exists(PARTS . DIRECTORY_SEPARATOR . $view . '.php')) {
    include_once(PARTS . DIRECTORY_SEPARATOR . '404.php');
} else {
    include_once(PARTS . DIRECTORY_SEPARATOR . $view . '.php');
}
$view_output = ob_get_clean();
if (!DOING_AJAX)
    include_once(PARTS . DIRECTORY_SEPARATOR . 'header.php');
echo $view_output;
if (!DOING_AJAX)
    include_once(PARTS . DIRECTORY_SEPARATOR . 'footer.php');
