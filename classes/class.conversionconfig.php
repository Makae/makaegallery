<?php

namespace ch\makae\makaegallery;

class ConversionConfig
{
    const DEFAULT_SUBDIR = 'converted';
    private string $prefix = 'resized-';
    private ?int $width = null;
    private ?int $height = null;
    private int $quality = 80;
    private string $resizeMode = ImageConverter::RESIZE_MODE_TO_DEFINED_DIMENSION;
    private string $subDir = self::DEFAULT_SUBDIR;

    public function __construct(?int $width, ?int $height, int $quality, string $resizeMode, string $subDir = self::DEFAULT_SUBDIR)
    {
        if($subDir === '' || strpos('.', $subDir) === 0 || strpos(DIRECTORY_SEPARATOR, $subDir) === 0) {
            throw new \InvalidArgumentException("`$subDir` is an invalid Subdirectory!");
        }
        if($resizeMode !== ImageConverter::RESIZE_MODE_NO_RESIZE && $width === null && $height === null) {
            throw new \InvalidArgumentException("Can not define a config without with or height. Only `no_resize` configs are allowed to do that.");
        }
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->resizeMode = $resizeMode;
        $this->subDir = $subDir;
    }

    public static function fromArray(array $config)
    {
        return new ConversionConfig(
            isset($config['width']) ? $config['width'] : null,
            isset($config['height']) ? $config['height'] : null,
            isset($config['quality']) ? min(max($config['quality'], 10), 100) : 80,
            isset($config['mode']) ? $config['mode'] : ImageConverter::RESIZE_MODE_TO_DEFINED_DIMENSION,
            isset($config['subDir']) ? $config['subDir'] : self::DEFAULT_SUBDIR
        );
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function hasWidth()
    {
        return $this->width !== null;
    }

    public function hasHeight()
    {
        return $this->height !== null;
    }

    public function getQuality()
    {
        return $this->quality;
    }

    public function getResizeMode()
    {
        return $this->resizeMode;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getSubDir(): ?string
    {
        return $this->subDir;
    }

    public function hasPrefix()
    {
        return $this->prefix !== null;
    }

    public function hasSubDir()
    {
        return $this->subDir !== null;
    }

}
