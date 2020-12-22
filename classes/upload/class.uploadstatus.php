<?php

namespace ch\makae\makaegallery;

class UploadStatus implements \JsonSerializable
{
    private string $name;
    private bool $success = true;
    private ?string $msg;

    public function __construct(string $name, ?string $msg = null)
    {
        $this->name = $name;
        $this->success = is_null($msg);
        $this->msg = $msg;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'success' => $this->success,
            'msg' => $this->msg
        ];
    }
}
