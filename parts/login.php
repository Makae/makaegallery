<?php
if(!defined('DOC_ROOT'))
    die();
    $username = isset($_REQUEST['name']) ? htmlentities($_REQUEST['name']) : '';
    $redirect = isset($redirect) ? $redirect : WWW_BASE . '/list';
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
    <label for="name">Name: </label> <input type="text" name="name" value="<?= $username ?>" /> <br/>
    <label for="password">Passwort: </label> <input type="password" name="password" /> <br/>
    <input type="submit" name="login" value="Anmelden" />
    <input type="hidden" name="redirect" value="<?= urlencode($redirect) ?>" />
</form>
