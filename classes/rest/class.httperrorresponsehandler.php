<?php

namespace ch\makae\makaegallery\rest;

use Exception;
use ReflectionClass;

class HttpErrorResponseHandler implements IHttpErrorResponseHandler
{
  private ?Exception $exception = null;

  public function handleException(Exception $exception): HttpResponse
  {
    $this->exception = $exception;
    $reflect = new ReflectionClass($exception);
    $exceptionName = strtolower($reflect->getShortName());
    return call_user_func([$this, $exceptionName . 'Handler']);
  }

  public function __call(string $name, array $arguments)
  {
    return HttpResponse::responseServerError('An unexpected error happened!');
  }

  public function wrongTenantExceptionHandler(): HttpResponse
  {
    return HttpResponse::responseUnauthorized('Wrong tenant!');
  }

  public function controllerDefinitionExceptionHandler(): HttpResponse
  {
    return HttpResponse::responseNotFound('Unknown Route!');
  }

  public function restAccessLevelExceptionHandler(): HttpResponse {
    return HttpResponse::responseUnauthorized('Not enough access right for this level!');
  }
  public function accessLevelExceptionHandler(): HttpResponse
  {
    return HttpResponse::responseUnauthorized('Not enough access rights!');
  }
}
