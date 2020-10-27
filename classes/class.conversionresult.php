<?php

namespace ch\makae\makaegallery;

class ConversionResult
{
    private string $originalPath;
    private string $conversionPath;
    private int $width;
    private int $height;

    public function __construct(string $originalPath, string $conversionPath, int $width, int $height)
    {
        $this->originalPath = $originalPath;
        $this->conversionPath = $conversionPath;
        $this->width = $width;
        $this->height = $height;
    }

    public function getOriginalPath(): string
    {
        return $this->originalPath;
    }

    public function getConversionPath(): string
    {
        return $this->conversionPath;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

}
