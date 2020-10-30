<?php

namespace ch\makae\makaegallery;

use DirectoryIterator;

class Gallery
{

    private string $identifier;
    private string $folder;
    private ?string $cover;
    private string $title;
    private string $description;
    private string $refText;
    private int $order;
    private int $level;
    private ?array $imageList = null;

    public function __construct(string $folder,
                                ?string $title,
                                ?string $cover = null,
                                string $description = '',
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
            isset($meta['cover']) ? $meta['cover'] : null,
            isset($meta['description']) ? $meta['description'] : null,
            isset($meta['refText']) ? $meta['refText'] : '',
            isset($meta['order']) ? $meta['order'] : 10,
            isset($meta['level']) ? $meta['level'] : Authentication::USER_LEVEL_ADMIN);
    }

    private static function sort($a, $b)
    {
        if ($a->getIdentifier() == $b->getIdentifier()) {
            return 0;
        }
        return ($a->getIdentifier() < $b->getIdentifier()) ? -1 : 1;
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
        $list = $this->getImages();
        foreach ($list as $img) {
            if ($img->getIdentifier() == $imageid) {
                return $img;
            }
        }
        return null;
    }

    public function getImages($reload = false)
    {
        if (!$reload && $this->imageList !== null) {
            return $this->imageList;
        }
        $this->imageList = $this->loadImagesFromDirectory($this->folder);
        return $this->imageList;
    }

    private function loadImagesFromDirectory($path)
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

        $imageList = [];
        $this->addImages($paths, $imageList);
        return $imageList;
    }

    private function addImages(array $paths, array &$images)
    {
        foreach ($paths as $path) {
            $images[] = $this->loadImageFromPath($path);
        }
        usort($images, [$this, 'sort']);
    }

    private function loadImageFromPath($path)
    {
        if (!preg_match('/^.*\.(jpg|jpeg|bmp|png)$/i', $path)) {
            return null;
        }

        $path = str_replace('\/', DIRECTORY_SEPARATOR, $path);
        return new Image(
            $this->getIdentifier() . '|' . basename($path),
            $path,
            null
        );

    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    private function addImage($path)
    {
        $this->addImages([$path], $this->imageList);
    }

}
