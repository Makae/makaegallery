<?php

class Gallery {
    private $images = array();
    private $identifier;
    private $folder;
    private $optimizer;
    private $thumbnailer;

    public function __construct($folder) {
        $this->folder = $folder;
        $this->identifier = basename($folder);
    }

    public function getIdentifier() {
        return $this->identifier;
    }

    public function getImageList($process=true) {
        $list = [];
        $pattern = '/^.*\.(jpg|jpeg|bmp|png)$/';

        $iterator = new \DirectoryIterator($this->folder);
        foreach($iterator as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            if($fileInfo->isFile() && preg_match($pattern, $fileInfo->getPathname())) {
                $original = $fileInfo->getPathname();

                $list[] = [
                    'imgid' => $this->getIdentifier() . '|' . basename($original),
                    'original_path' => $original,
                    'original_url'  => $this->getFileURL($original)
                ];
            }
        }
        if($process) {
            foreach($list as $image) {
                $image = $this->processImage($image);
            }
        }


        return $list;
    }

    public function processImage($image) {
        
        if(!file_exists($this->getResizeFolder())) {
            mkdir($this->getResizeFolder());
        }
        if(is_null($this->optimizer)) {
            $this->optimizer = new Processor("optimized", unserialize(PROCESS_CONFIG_NORMAL));
        }
        if(is_null($this->thumbnailer)){
            $this->thumbnailer = new Processor("thumb", unserialize(PROCESS_CONFIG_THUMB));
        }
        $optimized   = $this->optimizer->process($image);
        $thumbnailed = $this->thumbnailer->process($image);
        $image['optimized_url'] = $this->getFileUrl($optimized[1]);
        $image['thumbnail_url'] = $this->getFileUrl($thumbnailed[1]);
        return $image;
    }

    public function clearResized() {
        Utils::rmDir($this->folder . DIRECTORY_SEPARATOR . 'resized');
    }

    public function getResizeFolder() {
        return  $this->folder . DIRECTORY_SEPARATOR . 'resized';
    }

    public function getImage($imageid) {
        $list = $this->getImageList(false);
        foreach($list as $img) {
            if($img['imgid'] == $imageid)
                return $img;
        }
        return null;
    }

    private function getFileUrl($path) {
        $path = str_replace(ROOT, WWW_ROOT, $path);
        $path = str_replace('\\', '/', $path);
        return $path;
    }

    private function loadProcessed() {
        $cache = $this->folder . DIRECTORY_SEPARATOR . 'processed.cache';
        if(!file_exists($cache)) {
            return [];
        }
        return file_get_contents($cache);
    }

    private function saveProcessed($data) {
        $cache = $this->folder . DIRECTORY_SEPARATOR . 'processed.cache';
        file_put_contents($cache, serialize($data));
    }
}