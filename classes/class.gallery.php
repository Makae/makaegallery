<?php

namespace ch\makae\makaegallery;

use DirectoryIterator;

class Gallery
{
    const CACHE_KEY = 'gallery';

    private $identifier;
    private $folder;

    private string $cover;
    private string $title;
    private string $description;
    private string $refText;
    private int $order;
    private int $level;

    public function __construct(string $folder,
                                ?string $title,
                                string $description = '',
                                string $cover = GALLERY_DEFAULT_COVER,
                                string $refText = '',
                                int $order = 10,
                                int $level = Authentication::USER_LEVEL_ADMIN)
    {
        $this->folder = $folder;
        $this->identifier = basename($folder);
        $this->cover = $cover;
        $this->title = $title ? $title : $this->identifier;
        $this->description = $description;
        $this->refText = $refText;
        $this->order = $order;
        $this->level = $level;

        $this->imageList = $this->getCache();
    }

    private function getCache()
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        if ($cached = Utils::getCache($cache_path)) {
            return $cached;
        }
        return null;
    }

    public static function fromArray($folder, $meta)
    {
        return new Gallery(
            $folder,
            isset($meta['title']) ? $meta['title'] : null,
            isset($meta['description']) ? $meta['description'] : null,
            isset($meta['cover']) ? $meta['cover'] : null,
            isset($meta['refText']) ? $meta['refText'] : null,
            isset($meta['order']) ? $meta['order'] : null,
            isset($meta['level']) ? $meta['level'] : null);

    }

    private static function sort($a, $b)
    {
        if ($a['imgid'] == $b['imgid']) {
            return 0;
        }
        return ($a['imgid'] < $b['imgid']) ? -1 : 1;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getRefText()
    {
        return $this->refText;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getImage($imageid)
    {
        $list = $this->getImageList();
        foreach ($list as $img) {
            if ($img['imgid'] == $imageid) {
                return $img;
            }
        }
        return null;
    }

    public function getImageList()
    {
        if (is_null($this->imageList)) {
            $this->imageList = $this->loadImageListFromDir($this->folder);
            $this->updateCacheFile();
        }
        return $this->imageList;
    }

    private function loadImageListFromDir($path)
    {
        $iterator = new DirectoryIterator($path);
        $paths = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }
            if ($fileInfo->isFile()) {
                $paths[] = $fileInfo->getPathname();
            }
        }
        $this->addImages($paths);
        return $this->imageList;
    }

    private function loadImageFromPath($path)
    {
        static $pattern = '/^.*\.(jpg|jpeg|bmp|png)$/i';
        if (preg_match($pattern, $path)) {
            $original = str_replace('\/', DIRECTORY_SEPARATOR, $path);
            return [
                'imgid' => $this->getIdentifier() . '|' . basename($original),
                'path' => $original,
            ];
        }
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function updateCacheFile()
    {
        $this->setCache($this->imageList);
    }

    private function setCache($data)
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        Utils::setCache($cache_path, $data);
    }

    public function mergeImageData($imgid, $data)
    {
        $list = $this->getImageList();
        foreach ($list as $idx => $img) {
            if ($imgid === $img['imgid']) {
                $list[$idx] = array_merge($list[$idx], $data);
            }
        }

        $this->imageList = $list;
        $this->updateCacheFile();
    }

    public function addImages(array $paths)
    {
        foreach ($paths as $path) {
            $this->imageList[] = $this->loadImageFromPath($path);
        }
        usort($this->imageList, [$this, 'sort']);
        $this->updateCacheFile();
    }

    public function addImage($path)
    {
        $this->addImages([$path]);
    }

    public function clearCache()
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        Utils::clearCache($cache_path);
    }


}
