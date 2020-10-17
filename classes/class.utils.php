<?php
namespace ch\makae\makaegallery;

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

    public static function getUriComponents($url=null) {
        $url = is_null($url) ?  $_SERVER["REQUEST_URI"] : $url;
        $url = str_replace('/' . WWW_SUB_ROOT . '/', '', $url);
        preg_match_all("/(.*)(\?.+)?/", $url, $uri, PREG_PATTERN_ORDER);
        
        if(count($uri[0]) > 0) {
          $uri = $uri[1][0];
        } else {
          $uri = $_SERVER['REQUEST_URI'];
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

    public static function unTrailingSlashIt($url) {
      if(substr($url, 0, -1) == '/') {
        return substr($url, 0, -1);
      }
      return $url;
    }

    public static function setCache($path, $data) {
      file_put_contents($path, serialize($data));
    }

    public static function clearCache($path) {
      if(!file_exists($path)) {
        return;
      }
      unlink($path);
    }

    public static function getCache($path) {
      if(!file_exists($path)) {
        return false;
      }
      return unserialize(file_get_contents($path));
    }
}
