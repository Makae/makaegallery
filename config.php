<?php
// FIND SERVER ROOT PATH EXTENSION
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
$dir = $dir == '/' ? '' : $dir;
$ext = str_replace($root, '', $dir);

if(substr($ext, strlen($ext) - 1) == '/') {
  $ext = substr($ext, 0, -1);
}

if(substr($ext, 0, 1) == '/') {
  $ext = substr($ext, 1);
}

$domain = $_SERVER['SERVER_NAME'];
if(!preg_match('/^https?:\/\/.*/', $domain)) {
    $domain = '//' . $domain;
}

if(file_exists('config-env.php')) {
    require_once('config-env.php');
}

// GETTING PLACES
define('SUB_ROOT', str_replace('/', DIRECTORY_SEPARATOR, $ext));
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . SUB_ROOT . DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR );
define('GALLERY_ROOT', ROOT . 'galleries' . DIRECTORY_SEPARATOR);
define('PARTS', ROOT . 'parts' . DIRECTORY_SEPARATOR);

define('WWW_SUB_ROOT', $ext);
define('WWW_ROOT', $domain . '/');
define('WWW_BASE', $domain . '/' . (WWW_SUB_ROOT === "" ? "" :WWW_SUB_ROOT . '/'));
define('WWW_ASSETS', WWW_BASE . 'assets/');
define('WWW_GALLERY_ROOT', WWW_BASE . 'gallery/');

define('CONVERT_DEFAULT_PREFIX', DIRECTORY_SEPARATOR . 'resized');
define('TESTDIR', ROOT . 'tests' . DIRECTORY_SEPARATOR);
define('DOING_AJAX', isset($_REQUEST['ajax']));

@define('SALT', 'asdöfhöç2b4(&jwbj vyk sprog');

@define('AUTH_USERS', serialize(array(
    array(
        'name' => 'radmin',
        'password' => '1f5153edc921f1eee2e7916fdf98f0c6',
        'level' => 0,
    ),
    array(
        'name' => 'photobox',
        'password' => '4041ed306863ddde6c9ebf2c2676edb7',
        'level' => 1,
    ),
    array(
        'name' => 'besucher',
        'password' => '35b91af1d068598b2269aaf6cb56bfee',
        'level' => 2,
    )
)));

@define('AUTH_RESTRICTIONS', serialize(array(
    WWW_BASE . 'admin' => 0,
    WWW_BASE . 'view/photobox' => 1,
    WWW_GALLERY_ROOT . 'photobox' => 1,
    WWW_BASE . 'view' => 2,
    WWW_BASE . 'login' => 2
)));

@define('PROCESS_CONFIG_THUMB', serialize(array(
    'w' => 450,
    'q' => 80
)));

@define('PROCESS_CONFIG_NORMAL', serialize(array(
    'w' => 800,
    'h' => 800,
    'q' => 80,
    'm' => 'tosmaller'
)));

@define('PROCESS_CONFIG_ORIGINALS', serialize(array(
    'q' => 80,
    'm' => 'none'
)));

@define('GALLERY_CONFIGURATION', serialize(array(
)));

@define('GALLERY_DEFAULT_COVER', WWW_ASSETS . 'images/default_cover.jpg');
@define('GALLERY_COLUMNS', 3);
@define('GALLERY_IMAGES_PER_LOAD', 27);
