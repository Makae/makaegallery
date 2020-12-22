<?php

namespace ch\makae\makaegallery;

class UploadedFile
{

    private string $name;
    private string $tmpPath;
    private int $size;
    private int $error;

    public function __construct(string $name, string $tmpPath, int $size, int $error)
    {
        $this->name = $name;
        $this->tmpPath = $tmpPath;
        $this->size = $size;
        $this->error = $error;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

}
