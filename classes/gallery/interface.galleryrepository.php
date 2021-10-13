<?php

namespace ch\makae\makaegallery;

interface IGalleryRepository
{
  public function getAllowedImageTypes();

  public function clearProcessedImages($gallery_id);

  public function getGalleries(): array;

  public function processImageById($imgId);

  public function getGalleryByImageId(string $imgId, $ignoreCache = false, $process = true): PublicGallery;

  public function getGallery($gallery_id, $ignoreCache = false, $process = true): ?PublicGallery;

  public function getImageById($imgId);
}
