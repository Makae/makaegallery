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

    public function getImage($imageId, $ignoreCache=false, $process=true)
    {
        foreach ($this->getImages($ignoreCache, $process) as $image) {
            if ($image->getIdentifier() === $imageId) {
                return $image;
            }
        }
        return null;
    }

    public function getImages($ignoreCache=false, $process=true): array
    {
        $doCache = !$ignoreCache;
        if ($doCache && $this->cache->exists()) {
            return $this->cache->get()['images'];
        }
        $images = $this->gallery->getImages();
        if($process) {
            $images = $this->convertImages($images);
        }
        if($doCache) {
            $data = $this->cache->get();
            $data['images'] = $images;
            $data['processed'] = array_map(fn(Image $image) => $image->getIdentifier(), $images);
            $this->cache->set($data);
        }
        return $images;
    }

    private function convertImages($imageList)
    {
        array_walk($imageList, function (Image $image) {
            $this->convertImage($image);
            return true;
        });
        return $imageList;
    }

    private function getURLFromPath($path)
    {
        $url = str_replace($this->basePath, $this->baseUrl, $path);
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

    public function clearProcessed()
    {
        $this->converter->clear($this->gallery->getFolder());
        $this->cache->clear();
    }

    public function getResizeFolder()
    {
        return $this->gallery->getFolder() . DIRECTORY_SEPARATOR . 'resized';
    }

    public function processImageById($imageId)
    {
        $image = $this->convertImage($this->getImage($imageId, true, false));

        $data = $this->cache->getOrElse(['images' => [], 'processed' => []]);
        $pIdx = array_search($image->getIdentifier(), $data['processed']);
        $pIdx = $pIdx === false ? 0 : 1;
        $data['processed'][$pIdx] = $image->getIdentifier();

        return $image;
    }

    private function convertImage(Image $image): Image
    {
        $optimizingResult = $this->converter->convertTo(self::PROCESSOR_OPTIMIZED_KEY, $image->getSource());
        $thumbnailResult = $this->converter->convertTo(self::PROCESSOR_THUMBNAIL_KEY, $image->getSource());

        $image->setWidth($optimizingResult->getWidth());
        $image->setHeight($optimizingResult->getHeight());
        $image->setOriginalUrl($this->getURLFromPath($image->getSource()));
        $image->setOptimizedUrl($this->getURLFromPath($optimizingResult->getConversionPath()));
        $image->setThumbnailUrl($this->getURLFromPath($thumbnailResult->getConversionPath()));

        return $image;
    }


}
