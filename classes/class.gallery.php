<?php

namespace ch\makae\makaegallery;

class Gallery
{
    const CACHE_KEY = 'gallery';

    private $identifier;
    private $folder;
    private $meta;

    public function __construct($folder, $meta)
    {
        $this->folder = $folder;
        $this->identifier = basename($folder);
        $this->meta = $meta;
        $this->cache = $this->getCache();
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getCover()
    {
        return isset($this->meta['cover']) ? $this->meta['cover'] : GALLERY_DEFAULT_COVER;
    }

    public function getTitle()
    {
        return isset($this->meta['title']) ? $this->meta['title'] : $this->identifier;
    }

    public function getDescription()
    {
        return isset($this->meta['description']) ? $this->meta['description'] : '';
    }

    public function getRefText()
    {
        return isset($this->meta['ref_text']) ? $this->meta['ref_text'] : '<br />';
    }

    public function getOrder()
    {
        return isset($this->meta['order']) ? $this->meta['order'] : 20;
    }

    public function getLevel()
    {
        return isset($this->meta['level']) ? $this->meta['level'] : 0;
    }

    public function getLink()
    {
        return WWW_BASE . '/view/' . $this->getIdentifier();
    }


    public function getImageList()
    {
        if (is_null($this->cache)) {
            $this->cache = $this->getImageListFromDir($this->folder);
        }

        usort($this->cache, [$this, 'sort']);

        $this->updateCache();
        return $this->cache;
    }

    public function getPublicImageList()
    {
        $list = $this->getImageList();
        foreach ($list as &$image) {
            unset($image['original_path']);
        }
        return $list;
    }


    private function getImageListFromDir($path)
    {
        $pattern = '/^.*\.(jpg|jpeg|bmp|png)$/i';

        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            if ($fileInfo->isFile() && preg_match($pattern, $fileInfo->getPathname())) {
                $original = str_replace('\/', DIRECTORY_SEPARATOR, $fileInfo->getPathname());

                $list[] = [
                    'imgid' => $this->getIdentifier() . '|' . basename($original),
                    'original_path' => $original,
                    'original_url' => $this->getImageUrl($original)
                ];
            }
        }
        return $list;
    }

    private static function sort($a, $b)
    {
        if ($a['imgid'] == $b['imgid']) {
            return 0;
        }
        return ($a['imgid'] < $b['imgid']) ? -1 : 1;
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

    public function setImageData($new_image)
    {
        $list = $this->getImageList(false, false);
        foreach ($list as $idx => $img) {
            if ($img['imgid'] == $new_image['imgid']) {
                $list[$idx] = $new_image;
            }
        }

        $this->cache = $list;
        $this->updateCache();
    }

    private function getImageUrl($path)
    {
        $path = str_replace('//', '/', $path);
        $path = str_replace('\/', '/', $path);

        $path = str_replace($this->meta['root_dir'], $this->meta['url_base'], $path);
        $path = str_replace('\\', '/', $path);

        return str_replace(' ', '%20', $path);
    }

    private function getCache()
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        if ($cached = Utils::getCache($cache_path)) {
            return $cached;
        }
        return null;
    }

    public function clearCache()
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        Utils::clearCache($cache_path);
    }

    public function updateCache()
    {
        $this->setCache($this->cache);
    }

    private function setCache($data)
    {
        $cache_path = $this->folder . DIRECTORY_SEPARATOR . Gallery::CACHE_KEY . '.cache';
        Utils::setCache($cache_path, $data);
    }


}
