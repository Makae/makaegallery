<?php
if(!defined('DOC_ROOT'))
    die();
global $App;

?><!DOCTYPE html>
<html lang="de">
    <head>
        <!-- DEPENDENCIES -->
        <link rel="stylesheet" href="//yandex.st/bootstrap/3.1.1/css/bootstrap.min.css">
        <!-- CUSTOM STYLES -->
        <link rel="stylesheet" href="<?= WWW_ASSETS ?>/css/styles.css">
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script>
        $(document).on("mobileinit", function(){
            $.mobile.autoInitializePage=false;
            $.mobile.activePageClass='';
            $.mobile.ajaxEnabled=false;
            $.mobile.linkBindingEnabled=false;
            $.mobile.loadingMessage = false;
            $.mobile.page.prototype.options.keepNative = "select,input";
            $.mobile.loader.prototype.options.disabled = true;
        });            
        </script>
        <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script src="<?= WWW_ASSETS ?>/js/libs/tilt.jquery.min.js"></script>
        <script src="<?= WWW_ASSETS ?>/js/service.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <a href="<?= WWW_BASE . '/list' ?>" class="btn btn-primary">Zur Übersicht</a>
            <?php if($App->getAuth()->isLoggedIn()): ?>
            <a href="<?= WWW_BASE . '/list?logout=true' ?>" class="btn btn-default pull-right">Abmelden</a>
            <?php else: ?>
            <form action="<?= WWW_BASE . '/login' ?>" method="POST" class="pull-right">
            <input type="hidden" value="<?= WWW_BASE . '/list' ?>" name="redirect" />
            <input type="submit" value="Anmelden" class="btn btn-default" />
            </form>
            <?php endif; ?>
          </div>
        </nav>
        <div class="container">
            <!-- DYNAMICALLY LOADED -->
