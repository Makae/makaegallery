<?php

namespace ch\makae\makaegallery\rest;

use Exception;
use ReflectionClass;

interface IHttpErrorResponseHandler
{
  public function handleException(Exception $exception): HttpResponse;
}
