<?php

namespace ch\makae\makaegallery;

class ConversionConfig
{
    private string $prefix = 'resized-';
    private ?int $width = null;
    private ?int $height = null;
    private int $quality = 80;
    private string $resizeMode = GalleryConverter::RESIZE_MODE_TO_DEFINED_DIMENSION;
    private ?string $subDir = null;

    public function __construct(?int $width, ?int $height, int $quality, string $resizeMode, ?string $subDir = null)
    {
        if($resizeMode !== GalleryConverter::RESIZE_MODE_NO_RESIZE && $width === null && $height === null) {
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
            isset($config['w']) ? $config['w'] : null,
            isset($config['h']) ? $config['h'] : null,
            isset($config['q']) ? min(max($config['q'], 10), 100) : 80,
            isset($config['m']) ? $config['m'] : GalleryConverter::RESIZE_MODE_TO_DEFINED_DIMENSION,
            isset($config['s']) ? $config['s'] : null
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
