<?php
if(!defined('DOC_ROOT'))
    die();
    $username = isset($_REQUEST['name']) ? htmlentities($_REQUEST['name']) : '';
    $redirect = isset($redirect) ? $redirect : WWW_ROOT . '/list';
    $redirect = isset($_REQUEST['redirect']) ? urldecode($_REQUEST['redirect']) : $redirect;
    if(isset($_REQUEST['name'])) {
        $success = Authentication::instance()->login($username, $_REQUEST['password']);
        if($success) {
            header("Location: " . $redirect); 
            exit();
        }
    }
?>

<form method="POST">
    <input type="text" name="name" value="<?= $username ?>" />
    <input type="password" name="password" />
    <input type="submit" name="login" />
    <input type="hidden" name="redirect" value="<?= urlencode($redirect) ?>" />
</form>