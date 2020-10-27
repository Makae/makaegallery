<?php

namespace ch\makae\makaegallery;

class GalleryLoader
{
    private $galleryRoot;
    private $galleryMetas;

    public function __construct($galleryRoot, $galleryMetas)
    {
        $this->galleryRoot = $galleryRoot;
        $this->galleryMetas = $galleryMetas;
    }

    public function loadGalleries()
    {
        $galleries = [];
        foreach ($this->getGalleryDirs() as $dirname) {
            $folder = basename($dirname);
            if (isset($this->galleryMetas[$folder])) {
                $galleries[] = new Gallery(
                    $dirname,
                    $this->galleryMetas[$folder]
                );
            } else {
                $galleries[] = new Gallery($dirname, [
                    'title' => $folder,
                    'root_dir' => ROOT,
                    'url_base' => WWW_BASE,
                    'description' => $folder,
                    'level' => 0]
                );
            }
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
