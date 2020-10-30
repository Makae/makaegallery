<?php

namespace ch\makae\makaegallery;


class Image
{
    private string $identifier;
    private string $source;
    private ?string $image;
    private ?string $thumbnail;

    public function __construct(string $identifier, string $source, string $image = null, string $thumbnail = null)
    {
        $this->identifier = $identifier;
        $this->source = $source;
        $this->image = $image;
        $this->thumbnail = $thumbnail;
    }

    public static function fromArray(array $image): Image
    {
        return new Image(
            $image['id'],
            $image['source'],
            isset($image['image']) ? $image['image'] : null,
            isset($image['thumbnail']) ? $image['thumbnail'] : null,
        );
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function __unserialize(array $data): void
    {
        $this->identifier = $data['id'];
        $this->source = $data['src'];
        $this->image = $data['img'];
        $this->thumbnail = $data['thumb'];
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->identifier,
            'src' => $this->source,
            'img' => $this->image,
            'thumb' => $this->thumbnail
        ];
    }

}
