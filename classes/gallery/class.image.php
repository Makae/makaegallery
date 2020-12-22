<?php

namespace ch\makae\makaegallery;


class Image
{
    private string $identifier;
    private string $source;
    private ?string $originalUrl;
    private ?string $optimizedUrl;
    private ?string $thumbnailUrl;
    private ?int $height = null;
    private ?int $width = null;

    public function __construct(string $identifier, string $source, ?string $originalUrl = null, string $optimizedUrl = null, string $thumbnailUrl = null)
    {
        $this->identifier = $identifier;
        $this->source = $source;
        $this->originalUrl = $originalUrl;
        $this->optimizedUrl = $optimizedUrl;
        $this->thumbnailUrl = $thumbnailUrl;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getOptimizedUrl(): ?string
    {
        return $this->optimizedUrl;
    }

    public function setOptimizedUrl(?string $optimizedUrl): void
    {
        $this->optimizedUrl = $optimizedUrl;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(?string $thumbnailUrl): void
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    public function __unserialize(array $data): void
    {
        $this->identifier = $data['id'];
        $this->source = $data['src'];
        $this->width = $data['w'];
        $this->height = $data['h'];
        $this->originalUrl = $data['orig'];
        $this->optimizedUrl = $data['optim'];
        $this->thumbnailUrl = $data['thumb'];
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->identifier,
            'src' => $this->source,
            'w' => $this->width,
            'h' => $this->height,
            'orig' => $this->originalUrl,
            'optim' => $this->optimizedUrl,
            'thumb' => $this->thumbnailUrl
        ];
    }

    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl(string $originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

}
