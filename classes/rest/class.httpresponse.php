<?php

namespace ch\makae\makaegallery\rest;

class HttpResponse
{

  const STATUS_OK = 200;
  const STATUS_CREATED = 201;
  const STATUS_BAD_REQUEST = 400;
  const STATUS_UNAUTHORIZED = 401;
  const STATUS_FORBIDDEN = 403;
  const STATUS_NOT_FOUND = 404;
  const STATUS_SERVER_ERROR = 500;

  private int $status;
  private string $body;

  public function __construct(string $body, int $status = HttpResponse::STATUS_OK)
  {
    $this->status = $status;
    $this->body = $body;
  }

  public static function responseNotFound(string $body): HttpResponse
  {
    return new HttpResponse($body, self::STATUS_BAD_REQUEST);
  }

  public static function responseUnauthorized(string $body): HttpResponse
  {
    return new HttpResponse($body, self::STATUS_UNAUTHORIZED);
  }

  public static function responseOK(string $body = ''): HttpResponse
  {
    return new HttpResponse($body, self::STATUS_OK);
  }

  public static function responseServerError(string $body): HttpResponse
  {
    return new HttpResponse($body, self::STATUS_SERVER_ERROR);
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
