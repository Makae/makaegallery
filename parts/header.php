<?php
use ch\makae\makaegallery\security\Authentication;if (!defined('DOC_ROOT'))
    die();
global $App;

?><!DOCTYPE html>
<html lang="de">
<head>
    <!-- DEPENDENCIES -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW"
            crossorigin="anonymous"></script>

    <!-- CUSTOM STYLES -->
    <link rel="stylesheet" href="<?= WWW_ASSETS ?>/css/styles.css">
    <script src="<?= WWW_ASSETS ?>/js/libs/jquery.2.1.3.min.js"></script>
    <script>
        $(document).on("mobileinit", function () {
            $.mobile.autoInitializePage = false;
            $.mobile.activePageClass = '';
            $.mobile.ajaxEnabled = false;
            $.mobile.linkBindingEnabled = false;
            $.mobile.loadingMessage = false;
            $.mobile.page.prototype.options.keepNative = "select,input";
            $.mobile.loader.prototype.options.disabled = true;
        });
    </script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="<?= WWW_ASSETS ?>/js/libs/tilt.jquery.min.js"></script>
    <script src="<?= WWW_ASSETS ?>/js/service.js"></script>
</head>
<body class="view-<?php echo $App->getCurrentView() ?>">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= WWW_BASE ?>">Makae Gallery</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($App->getAuth()->hasAccessForLevel(Authentication::ACCESS_LEVEL_TENANT_ADMIN)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= WWW_BASE . '/users' ?>">Benutzer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= WWW_BASE . '/admin' ?>">Admin</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav d-flex justify-content-end">
                <?php if ($App->getAuth()->isAuthenticated()): ?>
                    <li class="nav-item nav-item-logout">
                        <a class="nav-link btn btn-primary" aria-current="page"
                           href="<?= WWW_BASE . '/login?logout=true' ?>">Abmelden</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <form action="<?= WWW_BASE . '/login' ?>" method="POST" class=>
                            <input type="hidden" value="<?= WWW_BASE . '/list' ?>" name="redirect"/>
                            <input type="submit" value="Anmelden" class="btn btn-default"/>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <!-- DYNAMICALLY LOADED -->
