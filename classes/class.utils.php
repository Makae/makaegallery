<?php

namespace ch\makae\makaegallery;

abstract class Utils
{

    public static function rmDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return Utils::rmFile($dir);
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

    public static function rmFile($file)
    {
        if (!file_exists($file)) {
            return true;
        }

        if (is_dir($file)) {
            throw new \InvalidArgumentException("Can not delete a directory via this method");
        }

        return unlink($file);
    }

    public static function getUriComponents($url = null)
    {
        $url = is_null($url) ? $_SERVER["REQUEST_URI"] : $url;
        $url = str_replace('/' . WWW_SUB_ROOT . '/', '', $url);
        $url = preg_replace('/([^?]+).*/', '$1', $url);
        $url = preg_replace('/(.*)\.php/', '$1', $url);
        preg_match_all("/(.*)(\?.+)?/", $url, $uri, PREG_PATTERN_ORDER);

        if (count($uri[0]) > 0) {
            $uri = $uri[1][0];
        } else {
            $uri = $_SERVER['REQUEST_URI'];
        }


        $uri = explode("/", $uri);
        foreach ($uri as $k => $v) {
            if ($v == '') {
                unset($uri[$k]);
            }
        }

        return array_values($uri);
    }

    public static function getHomeUrl()
    {
        return self::getAbsoluteUrlRoot() . self::getServerRoot();
    }

    public static function getAbsoluteUrlRoot()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol . $domainName;
    }

    public static function getServerRoot()
    {
        $root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
        $dir = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
        $ext = str_replace($root, '', $dir);
        if (substr($ext, strlen($ext) - 1) != '/') {
            $ext .= "/";
        }
        return $ext;
    }

    public static function unTrailingSlashIt($url)
    {
        if (substr($url, 0, -1) == '/') {
            return substr($url, 0, -1);
        }
        return $url;
    }
}
