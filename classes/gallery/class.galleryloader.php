<?php

namespace ch\makae\makaegallery;

use ch\makae\makaegallery\security\Authentication;

class GalleryLoader
{
  private $galleryRoot;
  private $galleryMetas;
  private ?string $defaultCover = null;

  public function __construct($galleryRoot, $galleryMetas, ?string $defaultCover = null)
  {
    $this->galleryRoot = $galleryRoot;
    $this->galleryMetas = $galleryMetas;
    $this->defaultCover = $defaultCover;
  }

  public function setDefaultCover(?string $cover)
  {
    $this->defaultCover = $cover;
  }

  public function loadGalleries()
  {
    $galleries = [];
    $defaults = [
      'cover' => $this->defaultCover,
      'level' => Authentication::ACCESS_LEVEL_ADMIN,
      'tenantId' => null
    ];
    foreach ($this->getGalleryDirs() as $dirname) {
      $folder = basename($dirname);
      if (isset($this->galleryMetas[$folder])) {
        $galleryArgs = array_merge($defaults, $this->galleryMetas[$folder]);
      } else {
        $galleryArgs = array_merge($defaults, ['title' => $folder, 'description' => $folder]);
      }
      $galleries[] = Gallery::fromArray(
        str_replace('/', DIRECTORY_SEPARATOR, $this->galleryRoot . DIRECTORY_SEPARATOR . $folder),
        $galleryArgs
      );
    }

    usort($galleries, function ($a, $b) {
      if ($a->getOrder() == $b->getOrder()) {
        return 0;
      }
      return $a->getOrder() < $b->getOrder() ? -1 : 1;
    });

    return $galleries;
  }

  private function getGalleryDirs()
  {
    return array_filter(glob($this->galleryRoot . "/*"), function ($dirname) {
      if (!is_dir($dirname) || strpos($dirname, '.') === 0) {
        return false;
      }
      return true;
    });
  }

}
