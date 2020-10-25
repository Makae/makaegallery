<?php

namespace ch\makae\makaegallery;

class ConversionConfig
{
    private string $prefix = 'resized';
    private ?int $width = null;
    private ?int $height = null;
    private int $quality = 80;
    private string $resizeMode = GalleryConverter::MODE_WIDTH_OR_HEIGHT;

    public function __construct(int $width, int $height, int $quality, int $resizeMode)
    {
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->resizeMode = $resizeMode;

    }

    public static function fromArray(array $config)
    {
        return new ConversionConfig(
            isset($config['w']) ? $config['w'] : null,
            isset($config['h']) ? $config['h'] : null,
            isset($config['q']) ? min(max($config['q'], 10, 100)) : 80,
            isset($config['m']) ? $config['m'] : 'worh'
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

    public function hasPrefix()
    {
        return $this->prefix !== null;
    }

}
