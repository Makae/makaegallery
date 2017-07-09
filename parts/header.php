<?php
if(!defined('DOC_ROOT'))
    die();
?><!DOCTYPE html>
<html lang="de">
    <head>
        <!-- DEPENDENCIES -->
        <link rel="stylesheet" href="//yandex.st/bootstrap/3.1.1/css/bootstrap.min.css">
        <!-- CUSTOM STYLES -->
        <link rel="stylesheet" href="<?= WWW_ASSETS ?>css/styles.css">

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>
        <script src="<?= WWW_ASSETS ?>js/service.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <a href="<?= WWW_ROOT . '/list' ?>" class="btn btn-primary">Zur Übersicht</a>
          </div>
        </nav>
        <div class="container">
            <!-- DYNAMICALLY LOADED -->