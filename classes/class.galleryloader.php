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
        $defaults = [
            'cover' => GALLERY_DEFAULT_COVER,
            'level' => 0
        ];
        foreach ($this->getGalleryDirs() as $dirname) {
            $folder = basename($dirname);
            if (isset($this->galleryMetas[$folder])) {
                $galleryArgs = array_merge($defaults, $this->galleryMetas[$folder]);
            } else {
                $galleryArgs = array_merge($defaults, ['title' => $folder, 'description' => $folder]);
            }
            $galleries[] = Gallery::fromArray(
                str_replace($folder, '\/', DIRECTORY_SEPARATOR),
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
