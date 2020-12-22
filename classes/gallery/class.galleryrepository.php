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

    public function processImageById($imgId)
    {
        $gallery = $this->getGalleryByImageId($imgId, true, false);
        return $gallery->processImageById($imgId);
    }

    public function getGalleryByImageId(string $imgId, $ignoreCache = false, $process = true): PublicGallery
    {
        $galleryId = explode('|', $imgId)[0];
        return $this->getGallery($galleryId, $ignoreCache, $process);
    }

    public function getGallery($gallery_id, $ignoreCache = false, $process = true): ?PublicGallery
    {
        foreach ($this->getGalleries() as $gallery) {
            if ($gallery->getIdentifier() == $gallery_id) {
                $gallery->getImages($ignoreCache, $process);
                return $gallery;
            }
        }
        return null;
    }

    public function getImageById($imgId)
    {
        list($galleryId, $imageId) = explode('|', $imgId);
        return $this->getGallery($galleryId)->getImage($imageId);
    }
}
