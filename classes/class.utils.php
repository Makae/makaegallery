<?php

abstract class Utils {

    public static function rmDir($dir) {
      if (!file_exists($dir)) {
          return true;
      }

      if (!is_dir($dir)) {
          return unlink($dir);
      }

      foreach (scandir($dir) as $item) {
          if ($item == '.' || $item == '..') {
              continue;
          }

          if (!Utils::rmDir($dir . DIRECTORY_SEPARATOR . $item)) {
              return false;
          }

      }

      return rmdir($dir);
    }

    public static function clearMinifiedImages() {
      global $galleries;
      foreach($galleries as $gallery) {
        $gallery->clearResized();
      }
    }

    public static function getGallery($gallery_id) {
      global $galleries;
      foreach($galleries as $gallery) {
        if($gallery->getIdentifier() == $gallery_id)
          return $gallery;
      }
      return null;
    }

    public static function getUriComponents($url=null) {
        $url = is_null($url) ?  $_SERVER["REQUEST_URI"] : $url;
        preg_match_all("/(.*)(\?.+)/", $url, $uri, PREG_PATTERN_ORDER);
        if(count($uri[0]) > 0) {
          $uri = $uri[1][0];
        } else {
          $uri = $_SERVER['REQUEST_URI'];
        }
        if(WWW_ROOT != '/') {
          $uri = str_replace(WWW_ROOT, "",$uri);
        }
        $uri = explode("/", $uri);
        foreach($uri as $k => $v) {
          if($v == '') {
            unset($uri[$k]);
          }
        }
        return array_values($uri);
    }

    public static function getServerRoot() {
      $root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
      $dir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
      $ext = str_replace($root, '', $dir);
      if(substr($ext, strlen($ext)-1) != '/') {
        $ext.="/";
      }
      return $ext;
    }

    public static function getHomeUrl() {
      return self::getAbsoluteUrlRoot().self::getServerRoot();
    }

    public static function getAbsoluteUrlRoot() {
      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $domainName = $_SERVER['HTTP_HOST'];
      return $protocol.$domainName;
    }
}
