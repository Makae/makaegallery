<?php

namespace ch\makae\makaegallery\security;

use DateTime;

class NonceToken
{
    private string $id = "";
    private DateTime $validUntil;

    public function __construct(string $id, DateTime $validUntil)
    {
        $this->id = $id;
        $this->validUntil = $validUntil;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValidUntil(): DateTime
    {
        return $this->validUntil;
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->validUntil = (new DateTime())->setTimestamp($data['valid']);
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'valid' => $this->validUntil->getTimestamp()
        ];
    }
}
