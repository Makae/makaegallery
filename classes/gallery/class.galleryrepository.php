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

    public function getGallery($gallery_id): ?PublicGallery
    {
        foreach ($this->getGalleries() as $gallery) {
            if ($gallery->getIdentifier() == $gallery_id) {
                return $gallery;
            }
        }
        return null;
    }

    public function processImageById($imgId)
    {
        $gallery = $this->getGalleryByImageId($imgId);
        return $gallery->processImageById($imgId);
    }

    private function getGalleryByImageId(string $imgId): PublicGallery
    {
        $galleryId = explode('|', $imgId)[0];
        return $this->getGallery($galleryId);

    }


}
