<?php

namespace ch\makae\makaegallery;

class GalleryRepository
{
    private array $galleries;
    private $allowedImageTypes;

    public function __construct(GalleryLoader $galleryLoader, ImageConverter $imageConverter)
    {
        $data = explode(",", ALLOWED_IMAGE_TYPES);
        $this->allowedImageTypes = array_map(fn($type) => "image/$type", $data);
        $this->galleries = [];
        foreach ($galleryLoader->loadGalleries() as $gallery) {
            $this->galleries[] = new PublicGallery($gallery, $imageConverter, ROOT, WWW_BASE);
        }
    }

    public function getAllowedImageTypes()
    {
        return $this->allowedImageTypes;
    }

    public function clearProcessedImages($gallery_id)
    {
        foreach ($this->getGalleries() as $gallery) {
            if (is_null($gallery_id) || $gallery->getIdentifier() == $gallery_id) {
                $gallery->clearProcessed();
            }
        }
    }

    public function getGalleries(): array
    {
        return $this->galleries;
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

    public function processImage($imgId)
    {
        $gallery = $this->getGalleryByImageId($imgId);
        return $gallery->processImageById($imgId);
    }

    private function getGalleryByImageId(string $imgId): PublicGallery
    {
        $galleryId = explode('|', $imgId)[0];
        return $this->getGallery($galleryId);

    }

    public function getGallery($gallery_id): ?PublicGallery
    {
        foreach ($this->getGalleries() as $gallery) {
            if ($gallery->getIdentifier() == $gallery_id)
                return $gallery;
        }
        return null;
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

    private function getImageUrl($path)
    {
        $path = str_replace('//', '/', $path);
        $path = str_replace('\/', '/', $path);

        $path = str_replace($this->meta['root_dir'], $this->meta['url_base'], $path);
        $path = str_replace('\\', '/', $path);

        return str_replace(' ', '%20', $path);
    }


}
