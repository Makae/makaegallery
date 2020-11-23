<?php

namespace ch\makae\makaegallery\rest;

class HttpResponse
{

    private int $status;
    private string $body;

    public function __construct(string $body, int $status = 200)
    {
        $this->status = $status;
        $this->body = $body;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getBody(): string
    {
        return $this->body;
    }

}
