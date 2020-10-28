<?php

namespace ch\makae\makaegallery;

use DirectoryIterator;

class Gallery
{

    private string $identifier;
    private string $folder;
    private string $cover;
    private string $title;
    private string $description;
    private string $refText;
    private int $order;
    private int $level;

    public function __construct(string $folder,
                                ?string $title,
                                string $description = '',
                                string $cover = '',
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
    }

    public static function fromArray($folder, $meta)
    {
        return new Gallery(
            $folder,
            isset($meta['title']) ? $meta['title'] : null,
            isset($meta['description']) ? $meta['description'] : null,
            isset($meta['cover']) ? $meta['cover'] : GALLERY_DEFAULT_COVER,
            isset($meta['refText']) ? $meta['refText'] : '',
            isset($meta['order']) ? $meta['order'] : 10,
            isset($meta['level']) ? $meta['level'] : Authentication::USER_LEVEL_ADMIN);

    }

    private static function sort($a, $b)
    {
        if ($a['imgid'] == $b['imgid']) {
            return 0;
        }
        return ($a['imgid'] < $b['imgid']) ? -1 : 1;
    }

    public function getFolder(): string
    {
        return $this->folder;
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
        return $this->loadImageListFromDir($this->folder);
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

    private function addImages(array $paths)
    {
        foreach ($paths as $path) {
            $this->imageList[] = $this->loadImageFromPath($path);
        }
        usort($this->imageList, [$this, 'sort']);
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

    private function addImage($path)
    {
        $this->addImages([$path]);
    }

}
