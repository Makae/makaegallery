<?php

namespace ch\makae\makaegallery\rest;

class HttpResponse
{

    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;

    private int $status;
    private string $body;

    public function __construct(string $body, int $status = HttpResponse::STATUS_OK)
    {
        $this->status = $status;
        $this->body = $body;
    }

    public static function responseNotFound(string $string)
    {
        return new HttpResponse($string, self::STATUS_BAD_REQUEST);
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
