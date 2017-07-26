<?php

class Gallery {
    private $images = array();
    private $identifier;
    private $folder;
    private $optimizer;
    private $thumbnailer;
    private $meta;

    public function __construct($folder, $meta) {
        $this->folder = $folder;
        $this->identifier = basename($folder);
        $this->meta = $meta;
    }

    public function getIdentifier() {
        return $this->identifier;
    }

    public function getCover() {
        return isset($this->meta['cover']) ? $this->meta['cover'] : GALLERY_DEFAULT_COVER;
    }

    public function getTitle() {
        return isset($this->meta['title']) ? $this->meta['title'] : $this->identifier;
    }

    public function getDescription() {
        return isset($this->meta['description']) ? $this->meta['description'] : '';
    }

    public function getRefText() {
        return isset($this->meta['ref_text']) ? $this->meta['ref_text'] : '<br />';
    }

    public function getLevel() {
        return isset($this->meta['level']) ? $this->meta['level'] : 0;
    }

    public function getLink() {
        return WWW_BASE . 'view/' . $this->getIdentifier();
    }

    public function getImageList($process=true, $nopaths=false, $cache_prefix=false) {
        $list = [];
        $pattern = '/^.*\.(jpg|jpeg|bmp|png)$/';

        $cache_path = $this->getResizeFolder() . $cache_prefix . '.cache';
        if($cache_prefix && ($cached = Utils::getCache($cache_path))) {
            return $cached;
        }

        $iterator = new \DirectoryIterator($this->folder);
        foreach($iterator as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            if($fileInfo->isFile() && preg_match($pattern, $fileInfo->getPathname())) {
                $original = str_replace('\/', DIRECTORY_SEPARATOR, $fileInfo->getPathname());

                $list[] = [
                    'imgid' => $this->getIdentifier() . '|' . basename($original),
                    'original_path' => $original,
                    'original_url'  => $this->getFileURL($original)
                ];
            }
        }
        
        foreach($list as &$image) {
            if($process) {
                $image = $this->processImage($image);
            }
            
            list($o_width, $o_height) = getImageSize($image['original_path']);
            $image['dimensions'] = [
                'width' => $o_width,
                'height' => $o_height
            ];

            if($nopaths) {
                unset($image['original_path']);
            }
        }

        usort($list, 'Gallery::_sort');
        if($cache_prefix)
            Utils::setCache($cache_path, $list);
        return $list;
    }

    private static function _sort($a, $b) {
        if ($a['imgid'] == $b['imgid']) {
            return 0;
        }
        return ($a['imgid'] < $b['imgid']) ? -1 : 1;
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
        $image['optimized_url'] = $this->getFileURL($optimized[1]);
        $image['thumbnail_url'] = $this->getFileURL($thumbnailed[1]);
        return $image;
    }

    public function clearResized() {
        Utils::rmDir($this->getResizeFolder());
    }

    public function getResizeFolder() {
        return  $this->folder . DIRECTORY_SEPARATOR . 'resized';
    }

    public function getImage($imageid, $cache_prefix='getImage') {
        $list = $this->getImageList(false, false, $cache_prefix);
        foreach($list as $img) {
            if($img['imgid'] == $imageid)
                return $img;
        }
        return null;
    }

    private function getFileURL($path) {
        $path = str_replace('//', '/', $path);
        $path = str_replace('\/', '/', $path);

        $path = str_replace(ROOT, WWW_BASE, $path);
        $path = str_replace('\\', '/', $path);

        return str_replace(' ', '%20', $path);
    }

}