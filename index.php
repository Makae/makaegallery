<?php
require_once('./config.php');

session_start();

foreach (glob("classes/trait.*.php") as $filename)
    require $filename;

foreach (glob("classes/class.*.php") as $filename)
    require $filename;

/*include_once('./tests/tests.php');*/

global $galleries;

$galleries = [];
$g_metas = unserialize(GALLERY_CONFIGURATION);
foreach (glob(GALLERY_ROOT . "/*") as $dirname) {
    if(!is_dir($dirname)) {
        continue;
    }

    if(strpos($dirname, '.') === 0) {
        continue;
    }

    $folder = basename($dirname);
    if(isset($g_metas[$folder])) {
        $meta = $g_metas[$folder];
    } else {
        $meta = array(
            'title' => $folder,
            'description' => $folder,
            'level' => 0
        );
    }
    $galleries[] = new Gallery($dirname, $meta);
    usort($galleries, function($a, $b) {
        if($a->getOrder() == $b->getOrder()) {
            return 0;
        }

        return $a->getOrder() < $b->getOrder() ? -1 : 1;
    });
}

$auth = Authentication::instance();
$auth->setUsers(unserialize(AUTH_USERS));
$auth->setRestrictions(unserialize(AUTH_RESTRICTIONS));


if(isset($_GET['logout']))   {
    $auth->logout();
}

$route = Utils::getUriComponents();
$view = isset($route[0]) ? $route[0] : 'list';

if(!Authentication::instance()->urlAllowed($_SERVER['REQUEST_URI'])) {
    $redirect = $_SERVER['REQUEST_URI'];
    $view = 'login';
}

ob_start();
if(!file_exists(PARTS . $view .'.php')) {
    include_once(PARTS .'404.php');
} else {
    include_once(PARTS . $view .'.php');
}
$view_output = ob_get_clean();
if(!DOING_AJAX)
    include_once(PARTS . 'header.php');
echo $view_output;
if(!DOING_AJAX)
    include_once(PARTS . 'footer.php');