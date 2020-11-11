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
    private ?array $images = null;

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

    public function addImageByName($fileName): ?Image
    {
        return $this->addImageAtPath($this->getFolder() . DIRECTORY_SEPARATOR . $fileName);
    }

    private function addImageAtPath($path): ?Image
    {
        $this->getImages();
        $this->removeImage($this->getImageIdentifier($path));
        $images = $this->appendImages($this->images, [$path]);
        if (count($images) === 0) {
            return null;
        }
        return $images[0];
    }

    public function removeImage(string $imageId)
    {
        if(is_null($this->images)) return;

        for ($idx = 0; $idx < count($this->images); $idx++) {
            if ($this->images[$idx]->getIdentifier() === $imageId) {
                unset($this->images[$idx]);
                return;
            }
        }
    }

    private function getImageIdentifier(string $path)
    {
        return $this->getIdentifier() . '|' . basename($path);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    private function appendImages(array &$images, array $paths): array
    {
        $newImages = [];
        foreach ($paths as $path) {
            $image = $this->appendImageFromPath($images, $path);
            if (!is_null($image)) {
                $newImages[] = $image;
            }
        }
        usort($images, [$this, 'sort']);
        return $newImages;
    }

    private function appendImageFromPath(array &$images, $path): ?Image
    {
        $image = $this->loadImageFromPath($path);
        if (is_null($image)) {
            return null;
        }
        $images[] = $image;
        return $image;
    }

    private function loadImageFromPath($path)
    {
        if (!preg_match('/^.*\.(jpg|jpeg|bmp|png)$/i', $path)) {
            return null;
        }

        $path = str_replace('\/', DIRECTORY_SEPARATOR, $path);
        return new Image(
            $this->getImageIdentifier($path),
            $path,
            null
        );

    }

    public function getFolder(): string
    {
        return $this->folder;
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
        if (!$reload && $this->images !== null) {
            return $this->images;
        }
        $this->images = $this->loadImagesFromDirectory($this->folder);
        return $this->images;
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

        $images = [];
        $this->appendImages($images, $paths);
        return $images;
    }

}
