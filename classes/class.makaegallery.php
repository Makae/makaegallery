<?php

namespace ch\makae\makaegallery;

class MakaeGallery
{
    private $galleries;
    private $galleryConverter;

    public function __construct(GalleryLoader $galleryLoader, GalleryConverter $galleryConverter)
    {
        $this->galleries = $galleryLoader->loadGalleries();
        $this->galleryConverter = $galleryConverter;
    }

    public function clearMinifiedImages($gallery_id)
    {
        foreach ($this->getGalleries() as $gallery) {
            if (is_null($gallery_id) || $gallery->getIdentifier() == $gallery_id) {
                $gallery->clearResized();
            }
        }
    }

    public function getGalleries()
    {
        return $this->galleries;
    }

    public function updateImageList($gallery_id)
    {
        $gallery = $this->getGallery($gallery_id);
        if ($gallery === null) {
            return null;
        }

        return $gallery->updateImageList();
    }

    public function getGallery($gallery_id)
    {
        foreach ($this->getGalleries() as $gallery) {
            if ($gallery->getIdentifier() == $gallery_id)
                return $gallery;
        }
        return null;
    }

    public function getPublicImageList()
    {
        $list = $this->getImageList();
        foreach ($list as &$image) {
            unset($image['original_path']);
        }
        return $list;
    }

    public function clearResized()
    {
        Utils::rmDir($this->getResizeFolder());
        $this->clearCache();
    }

    public function getResizeFolder()
    {
        return $this->folder . DIRECTORY_SEPARATOR . 'resized';
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

    private function getImageUrl($path)
    {
        $path = str_replace('//', '/', $path);
        $path = str_replace('\/', '/', $path);

        $path = str_replace($this->meta['root_dir'], $this->meta['url_base'], $path);
        $path = str_replace('\\', '/', $path);

        return str_replace(' ', '%20', $path);
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


}
