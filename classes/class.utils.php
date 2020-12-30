<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\security\Authentication;

abstract class Utils
{

    public static function mapImagesToArray(array $images)
    {
        return array_map(
            "\ch\makae\makaegallery\Utils::mapImageToArray",
            $images);
    }

    public static function mapImageToArray(Image $image)
    {
        return [
            'id' => $image->getIdentifier(),
            'thumbnail_url' => $image->getThumbnailUrl(),
            'optimized_url' => $image->getOptimizedUrl(),
            'original_url' => $image->getOriginalUrl(),
            'dimensions' => ['width' => $image->getWidth(), 'height' => $image->getHeight()]
        ];
    }

    public static function arraySearch(array $array, callable $comparitor)
    {
        return array_filter(
            $array,
            function ($element) use ($comparitor) {
                if ($comparitor($element)) {
                    return $element;
                }
            }
        );
    }

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

    public static function getUriComponents($uri = null)
    {
        $uri = explode("/", $uri);
        foreach ($uri as $k => $v) {
            if ($v == '') {
                unset($uri[$k]);
            }
        }

        return array_values($uri);
    }

    public static function getRequestUri($url = null): string
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
        return $uri;
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

    public static function getAllHeaders()
    {
        foreach ($_SERVER as $K => $V) {
            $a = explode('_', $K);
            if (array_shift($a) == 'HTTP') {
                array_walk($a, function (&$v) {
                    $v = ucfirst(strtolower($v));
                });
                $retval[join('-', $a)] = $V;
            }
        }
        return $retval;
    }

    public static function getAccessLevelName(int $level): string
    {
        switch ($level) {
            case Authentication::ACCESS_LEVEL_ADMIN:
                return "ADMIN";
            case Authentication::ACCESS_LEVEL_USER:
                return "USER";
            case Authentication::ACCESS_LEVEL_GUEST:
                return "GUEST";
            case Authentication::ACCESS_LEVEL_PUBLIC:
            default:
                return "PUBLIC";
        }
    }

}
