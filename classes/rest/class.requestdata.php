<?php

namespace ch\makae\makaegallery\rest;

class RequestData
{

    private array $parameters;
    private array $header;
    private array $body;

    public function __construct(array $parameters, array $header, array $body)
    {
        $this->parameters = $parameters;
        $this->header = $header;
        $this->body = $body;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
