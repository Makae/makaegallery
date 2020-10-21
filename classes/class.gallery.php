<?php

namespace ch\makae\makaegallery;

class Gallery
{
    const CACHE_KEY = 'gallery';

    private $identifier;
    private $folder;
    private $optimizer;
    private $thumbnailer;
    private $meta;

    public function __construct($folder, $meta, Processor $optimizer, Processor $thumbnailer)
    {
        $this->folder = $folder;
        $this->identifier = basename($folder);
        $this->meta = $meta;
        $this->cache = $this->getCache();

        $this->optimizer = $optimizer;
        $this->thumbnailer = $thumbnailer;
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


    public function getImageList($process = true, $meta = true)
    {
        if (is_null($this->cache)) {
            $this->cache = $this->getImageListFromDir($this->folder);
        }

        foreach ($this->cache as &$image) {
            $image = $this->prepareImage($image, $process, $meta);
        }

        usort($this->cache, [$this, 'sort']);

        $this->updateCache();

        return $this->cache;
    }

    public function getPublicImageList($process = true, $meta = true)
    {
        $list = $this->getImageList($process, $meta);
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

    public function processImage($image)
    {
        if (isset($image['processed'])) {
            return $image;
        }

        if (!file_exists($this->getResizeFolder())) {
            mkdir($this->getResizeFolder());
        }

        $optimized = $this->optimizer->process($image);
        $thumbnailed = $this->thumbnailer->process($image);

        $image['processed'] = true;
        $image['optimized_url'] = $this->getImageUrl($optimized[1]);
        $image['thumbnail_url'] = $this->getImageUrl($thumbnailed[1]);

        return $image;
    }

    public function addImageMeta($image)
    {
        if (isset($image['meta_added'])) {
            return $image;
        }

        $image['meta_added'] = true;
        list($o_width, $o_height) = getImageSize($image['original_path']);
        $image['dimensions'] = [
            'width' => $o_width,
            'height' => $o_height
        ];
        return $image;
    }

    public function clearResized()
    {
        Utils::rmDir($this->getResizeFolder());
        $this->clearCache();
    }

    public function updateImageList()
    {
        $diff = $this->getCacheDifference();
        // TODO: return diff and reprocess images which are new.
        foreach($diff['new'] as $newImg) {

        }
        // refresh cache
        return $diff;
    }

    public function getCacheDifference()
    {
        $oldImages = $this->asImageMap($this->getCache());
        $newImages = $this->asImageMap($this->getImageListFromDir($this->folder));

        $oldImageKeys = array_keys($oldImages);
        $newImageKeys = array_keys($newImages);

        $removedImageKeys = array_diff($oldImageKeys, $newImageKeys);
        $addedImageKeys = array_diff($newImageKeys, $oldImageKeys);

        $diff = [
            'added' => [],
            'removed' => []
        ];
        foreach ($addedImageKeys as $key) {
            $diff['added'][] = $newImages[$key];
        }
        foreach ($removedImageKeys as $key) {
            $diff['removed'][] = $oldImages[$key];
        }
        return $diff;
    }

    private function asImageMap($array)
    {
        $map = [];

        foreach ($array as $img) {
            $map[$img['id']] = $img;
        }

        return $map;
    }

    public function getResizeFolder()
    {
        return $this->folder . DIRECTORY_SEPARATOR . 'resized';
    }

    public function getImage($imageid, $process = false, $meta = false)
    {
        $list = $this->getImageList($process, $meta, true, true);
        foreach ($list as $img) {
            if ($img['imgid'] == $imageid) {
                return $img;
            }
        }
        return null;
    }

    public function setImageData($new_image, $update_cache = true)
    {
        $list = $this->getImageList(false, false);
        foreach ($list as $idx => $img) {
            if ($img['imgid'] == $new_image['imgid']) {
                $list[$idx] = $new_image;
            }
        }

        $this->cache = $list;
        if ($update_cache) {
            $this->updateCache();
        }
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

    private function prepareImage($image, $process, $meta)
    {
        if ($process) {
            $image = $this->processImage($image);
        }

        if ($meta) {
            $image = $this->addImageMeta($image);
        }
        return $image;
    }

}
