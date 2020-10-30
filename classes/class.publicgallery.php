<?php

namespace ch\makae\makaegallery;


class PublicGallery
{
    const CACHE_KEY = 'gallery.cache';

    const PROCESSOR_OPTIMIZED_KEY = 'optimized';
    const PROCESSOR_THUMBNAIL_KEY = 'thumbnail';

    private $basePath;
    private $baseUrl;
    private Cache $cache;

    private Gallery $gallery;
    private ImageConverter $converter;

    public function __construct(Gallery $gallery, ImageConverter $converter, string $basePath, string $baseUrl)
    {
        $this->gallery = $gallery;
        $this->converter = $converter;
        $this->basePath = $basePath;
        $this->baseUrl = $baseUrl;

        $this->cache = new Cache($this->gallery->getFolder() . DIRECTORY_SEPARATOR . PublicGallery::CACHE_KEY);
    }

    public function getCover()
    {
        return $this->gallery->getCover();
    }

    public function getTitle()
    {
        return $this->gallery->getTitle();
    }

    public function getDescription()
    {
        return $this->gallery->getDescription();
    }

    public function getRefText()
    {
        return $this->gallery->getRefText();
    }

    public function getOrder()
    {
        return $this->gallery->getOrder();
    }

    public function getLevel()
    {
        return $this->gallery->getLevel();
    }

    public function getImage($imageId)
    {
        foreach ($this->getImages() as $key => $image) {
            if ($image->getIdentifier() === $imageId) {
                return $image;
            }
        }
        return null;
    }

    public function getImages(): array
    {
        if ($this->cache->exists()) {
            return $this->cache->get();
        }

        $images = $this->gallery->getImages();
        $images = $this->convertImages($images);

        $this->cache->set($images);
        return $images;
    }

    private function convertImages($imageList)
    {
        array_walk($imageList, function (Image $image) {
            $optimizingResult = $this->converter->convertTo(self::PROCESSOR_OPTIMIZED_KEY, $image->getSource());
            $thumbnailResult = $this->converter->convertTo(self::PROCESSOR_THUMBNAIL_KEY, $image->getSource());

            $image->setOriginalUrl($this->getURLFromPath($image->getSource()));
            $image->setOptimizedUrl($this->getURLFromPath($optimizingResult->getConversionPath()));
            $image->setThumbnailUrl($this->getURLFromPath($thumbnailResult->getConversionPath()));
            return true;
        });
        return $imageList;
    }

    private function getURLFromPath($path) {
        $url = str_replace(BASE_PATH, BASE_URL, $path);
        $url = str_replace(DIRECTORY_SEPARATOR, '/', $url);
        return $url;
    }

    public function getLink()
    {
        return $this->baseUrl . '/view/' . $this->getIdentifier();
    }

    public function getIdentifier()
    {
        return $this->gallery->getIdentifier();
    }

    public function clearResized()
    {
        Utils::rmDir($this->getResizeFolder());
        $this->cache->clear();
    }

    public function getResizeFolder()
    {
        return $this->gallery->getFolder() . DIRECTORY_SEPARATOR . 'resized';
    }


}
