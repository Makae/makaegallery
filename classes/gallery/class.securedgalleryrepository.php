<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\rest\AccessLevelException;
use ch\makae\makaegallery\rest\UserAuthenticationException;
use ch\makae\makaegallery\rest\WrongTenantException;
use ch\makae\makaegallery\security\Authentication;

class SecuredGalleryRepository implements IGalleryRepository
{

  private IGalleryRepository $galleryRepository;
  private Authentication $authentication;
  private array $galleryAccess;

  public function __construct(IGalleryRepository $galleryRepository, Authentication $authentication, array $galleryAccess)
  {
    $this->galleryRepository = $galleryRepository;
    $this->authentication = $authentication;
    $this->galleryAccess = $galleryAccess;
  }

  public function getAllowedImageTypes()
  {
    return $this->galleryRepository->getAllowedImageTypes();
  }

  public function clearProcessedImages($gallery_id)
  {
    $this->throwIfNoAccessToGallery($gallery_id);
    return $this->galleryRepository->clearProcessedImages($gallery_id);
  }

  private function throwIfNoAccessToGallery($galleryId)
  {
    $galleryAccess = $this->getGalleryAccess($galleryId);
    if (!$galleryAccess) {
      throw new AccessLevelException('No gallery access specified');
    }
    if ($this->authentication->isSuperAdmin()) {
      return;
    }
    if (!$this->hasAccessToTenant($galleryAccess['tenantId'])) {
      throw new WrongTenantException('Unauthorized');
    }
    if (!$this->authentication->hasAccessForLevel($galleryAccess['level'])) {
      throw new AccessLevelException('Forbidden');
    }
  }

  private function getGalleryAccess($galleryId)
  {
    foreach ($this->galleryAccess as $id => $galleryAccess) {
      if ($id === $galleryId) {
        return $galleryAccess;
      }
    }
    return null;
  }

  private function hasAccessToTenant($tenantId): bool
  {
    if ($tenantId === null) {
      throw new \Exception("TenantId is required in gallery definition");
    }

    if (!$this->authentication->isAuthenticated()) {
      return false;
    }

    try {
      return $this->authentication->getTenantId() === $tenantId;
    } catch (UserAuthenticationException $e) {
      return false;
    }
  }

  /**
   * @return Gallery[] $gallery
   */
  public function getGalleries(): array
  {
    return $this->filterGalleries($this->galleryRepository->getGalleries());
  }

  /**
   * @return PublicGallery[] $gallery
   */
  private function filterGalleries(array $galleries): array
  {
    return array_filter($galleries, function (PublicGallery $gallery) {
      try {
        $this->throwIfNoAccessToGallery($gallery->getIdentifier());
        return true;
      } catch (WrongTenantException | AccessLevelException $e) {
        return false;
      }
    });
  }

  public function processImageById($imgId)
  {
    $this->throwIfNoAccessToImage($imgId);
    return $this->galleryRepository->processImageById($imgId);
  }

  private function throwIfNoAccessToImage(string $imgId)
  {
    $gallery = $this->galleryRepository->getGalleryByImageId($imgId);
    $this->throwIfNoAccessToGallery($gallery->getIdentifier());
  }

  public function getGalleryByImageId(string $imgId, $ignoreCache = false, $process = true): PublicGallery
  {
    $this->throwIfNoAccessToImage($imgId);
    return $this->galleryRepository->getGalleryByImageId($imgId, $ignoreCache, $process);
  }

  public function getGallery($gallery_id, $ignoreCache = false, $process = true): ?PublicGallery
  {
    $this->throwIfNoAccessToGallery($gallery_id);
    return $this->galleryRepository->getGallery($gallery_id, $ignoreCache, $process);
  }

  public function getImageById($imgId)
  {
    $this->throwIfNoAccessToImage($imgId);
    return $this->galleryRepository->getImageById($imgId);
  }

}
