<?php
use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\ImageConverter;

// FIND SERVER ROOT PATH EXTENSION
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
$dir = $dir == '/' ? '' : $dir;
$subFolder = str_replace($root, '', $dir);

if (substr($subFolder, strlen($subFolder) - 1) == '/') {
    $subFolder = substr($subFolder, 0, -1);
}

if (substr($subFolder, 0, 1) == '/') {
    $subFolder = substr($subFolder, 1);
}

$domain = $_SERVER['SERVER_NAME'];
if (!preg_match('/^https?:\/\/.*/', $domain)) {
    $domain = '//' . $domain;
}

if (file_exists('config-env.php')) {
    require_once('config-env.php');
}

// GETTING PLACES
define('SUB_ROOT', str_replace('/', DIRECTORY_SEPARATOR, $subFolder));
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . SUB_ROOT);
define('ROOT', dirname(__FILE__));
define('GALLERY_FOLDER', 'galleries');
define('GALLERY_ROOT', ROOT . DIRECTORY_SEPARATOR . GALLERY_FOLDER);
define('PARTS_DIR', ROOT . DIRECTORY_SEPARATOR . 'parts');

define('WWW_SUB_ROOT', $subFolder);
define('WWW_ROOT', $domain);
define('WWW_BASE', $domain . (WWW_SUB_ROOT === "" ? "" : '/' . WWW_SUB_ROOT));
define('WWW_ASSETS', WWW_BASE . '/assets');
define('WWW_GALLERY_ROOT', WWW_SUB_ROOT . '/' . GALLERY_FOLDER);

define('ALLOWED_IMAGE_TYPES', 'png,jpeg,bmp');

define('TESTDIR', ROOT . 'tests' . DIRECTORY_SEPARATOR);
define('DOING_AJAX', isset($_REQUEST['ajax']));

@define('SALT', 'asdöfhöç2b4(&jwbj vyk sprog');

@define('AUTH_USERS', serialize(array(
    array(
        'name' => 'radmin',
        'password' => '1f5153edc921f1eee2e7916fdf98f0c6',
        'level' => Authentication::ACCESS_LEVEL_ADMIN,
    ),
    array(
        'name' => 'photobox',
        'password' => '4041ed306863ddde6c9ebf2c2676edb7',
        'level' => Authentication::ACCESS_LEVEL_USER,
    ),
    array(
        'name' => 'besucher',
        'password' => '35b91af1d068598b2269aaf6cb56bfee',
        'level' => Authentication::ACCESS_LEVEL_GUEST,
    )
)));

@define('AUTH_RESTRICTIONS', serialize(array(
    'admin' => Authentication::ACCESS_LEVEL_ADMIN,
    'view/photobox' => Authentication::ACCESS_LEVEL_USER,
    'galleries/photobox' => Authentication::ACCESS_LEVEL_USER,
    'view' => Authentication::ACCESS_LEVEL_GUEST,
    'masonry' => Authentication::ACCESS_LEVEL_GUEST,
    'login' => Authentication::ACCESS_LEVEL_PUBLIC,
    'list' => Authentication::ACCESS_LEVEL_PUBLIC
)));

@define('PROCESS_CONFIG_THUMB', serialize([
    'width' => 450,
    'quality' => 80,
    'mode' => ImageConverter::RESIZE_MODE_TO_DEFINED_DIMENSION
]));

@define('PROCESS_CONFIG_NORMAL', serialize([
    'width' => 800,
    'height' => 800,
    'quality' => 80,
    'mode' => ImageConverter::RESIZE_MODE_TO_SMALLER
]));

@define('PROCESS_CONFIG_ORIGINALS', serialize([
    'quality' => 80,
    'mode' => ImageConverter::RESIZE_MODE_NO_RESIZE
]));

@define('GALLERY_CONFIGURATION', serialize(array()));

@define('GALLERY_DEFAULT_COVER', WWW_ASSETS . '/images/default_cover.jpg');
@define('GALLERY_COLUMNS', 3);
@define('GALLERY_IMAGES_PER_LOAD', 27);
