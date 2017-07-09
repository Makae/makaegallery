<?php
// FIND SERVER ROOT PATH EXTENSION
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
$ext = str_replace($root, '', $dir);
if(substr($ext, strlen($ext)-1) != '/') {
  $ext.="/";
}

if(file_exists('config-env.php')) {
    require_once('config-env.php');
}

// GETTING PLACES
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].$ext);
define('WWW_ROOT', $ext);
define('WWW_ASSETS', WWW_ROOT . 'assets/');
define('WWW_GALLERY_ROOT', WWW_ROOT . 'gallery/');

define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR );
define('GALLERY_ROOT', ROOT . 'galleries' . DIRECTORY_SEPARATOR);
define('PARTS', ROOT . 'parts' . DIRECTORY_SEPARATOR);

define('CONVERT_DEFAULT_PREFIX', DIRECTORY_SEPARATOR . 'resized');

define('TESTDIR', ROOT . 'tests' . DIRECTORY_SEPARATOR);

define('DOING_AJAX', isset($_REQUEST['ajax']));

define('SALT', 'asdöfhöç2b4(&jwbj vyk sprog');

define('AUTH_USERS', serialize(array(
    array(
        'name' => 'radmin',
        'password' => 'be814a82404fcfb0099c5b3f9db51a7e',
        'level' => 2,
    ),
    array(
        'name' => 'besucher',
        'password' => 'b5acc84295c979fbbb8a597f7e32b31d',
        'level' => 1,
    )
)));

define('AUTH_RESTRICTIONS', serialize(array(
    WWW_ROOT . 'admin' => 2,
    WWW_ROOT . 'view/photobox' => 1,
    WWW_GALLERY_ROOT . 'photobox' => 1
)));

define('PROCESS_CONFIG_THUMB', serialize(array(
    'w' => 450,
    'q' => 80
)));

define('PROCESS_CONFIG_NORMAL', serialize(array(
    'w' => 800,
    'h' => 800,
    'q' => 80,
    'm' => 'tosmaller'
)));

@define('GALLERY_CONFIGURATION', serialize(array(
)));

define('GALLERY_DEFAULT_COVER', WWW_ASSETS . 'images/default_cover.jpg');
define('GALLERY_COLUMNS', 3);
define('GALLERY_IMAGES_PER_LOAD', 27);