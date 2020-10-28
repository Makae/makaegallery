<?php

namespace ch\makae\makaegallery;


class PublicGallery
{
    const CACHE_KEY = 'gallery.cache';

    private $basePath;
    private $baseUrl;
    private Cache $cache;

    public function __construct(Gallery $gallery, ImageConverter $imageConverter, string $basePath, string $baseUrl)
    {
        $this->gallery = $gallery;
        $this->imageConverter = $imageConverter;
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

    public function getImage($imageid)
    {
        return $this->gallery->getImage($imageid);
    }

    public function getLink() {
        return $this->baseUrl . '/view/' . $this->getIdentifier();
    }

    public function getImageList()
    {
        if ($this->cache->exists()) {
            return $this->cache->get()['imageList'];
        }

        $img_list = $this->gallery->getImageList();
        $img_list = $this->convertImages($img_list);


        $this->cache->set(['imageList' => $img_list]);
        return $img_list;
    }


    public function clearResized()
    {
        Utils::rmDir($this->getResizeFolder());
        $this->cache->clear();
    }

    public function getResizeFolder()
    {
        return $this->folder . DIRECTORY_SEPARATOR . 'resized';
    }

    private function convertImages($img_list)
    {
        array_walk($img_list, function ($elm) {
            $elm['src'] = str_replace($this->basePath, $this->baseUrl, $elm['path']);
            $elm['thumb'] = $this->imageConverter->convertTo('thumb', $elm['path']);
            $elm['optimized'] = $this->imageConverter->convertTo('optimized', $elm['path']);

            unset($elm['path']);
            return $elm;
        });
        return $img_list;
    }

    public function getIdentifier()
    {
        return $this->gallery->getIdentifier();
    }


}
