<?php

namespace ch\makae\makaegallery;


use function file_exists;
use function file_put_contents;
use function serialize;
use function unlink;

class Cache
{
    private $cacheFile;
    private $value;

    public function __construct(string $cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    public function clear()
    {
        if (!file_exists($this->cacheFile)) {
            return;
        }
        unlink($this->cacheFile);
        $this->value = null;
    }

    public function set($data)
    {
        $this->value = $data;
        file_put_contents($this->cacheFile, serialize($data));
    }

    public function get()
    {
        if ($this->value !== null) {
            return $this->value;
        }
        if (!file_exists($this->cacheFile)) {
            return null;
        }
        return unserialize(file_get_contents($this->cacheFile));
    }

    public function exists()
    {
        return $this->get() !== null;
    }
}
