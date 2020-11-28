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

    public function getMethod(): string {
        return isset($header['REQUEST_METHOD']) ? $header['REQUEST_METHOD'] : "GET";
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }
}
