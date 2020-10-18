<?php

namespace ch\makae\makaegallery;

class MakaeGallery
{
    private $galleries;
    private $galleryRoot;
    private $optimizer;
    private $thumbnailer;

    public function __construct($galleryRoot, $galleryMetas, Processor $optimizer, Processor $thumbnailer)
    {
        $this->galleryRoot = $galleryRoot;
        $this->galleryMetas = $galleryMetas;

        $this->optimizer = $optimizer;
        $this->thumbnailer = $thumbnailer;

        $this->galleries = $this->loadGalleries();
    }

    private function loadGalleries()
    {
        $galleries = [];
        foreach ($this->getGalleryDirs() as $dirname) {
            $folder = basename($dirname);
            if (isset($this->galleryMetas[$folder])) {
                $galleries[] = new Gallery(
                    $dirname,
                    $this->galleryMetas[$folder],
                    $this->optimizer,
                    $this->thumbnailer);
            } else {
                $galleries[] = new Gallery($dirname, [
                    'title' => $folder,
                    'description' => $folder,
                    'level' => 0],
                    $this->optimizer,
                    $this->thumbnailer);
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

    public function getGalleries()
    {
        return $this->galleries;
    }

    public function clearMinifiedImages($gallery_id)
    {
        foreach ($this->getGalleries() as $gallery) {
            if (is_null($gallery_id) || $gallery->getIdentifier() == $gallery_id) {
                $gallery->clearResized();
            }
        }
    }

    public function getGallery($gallery_id)
    {
        foreach ($this->getGalleries() as $gallery) {
            if ($gallery->getIdentifier() == $gallery_id)
                return $gallery;
        }
        return null;
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
