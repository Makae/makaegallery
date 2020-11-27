<?php

namespace ch\makae\makaegallery\rest;

interface IRestController
{
    public function handle(string $method, string $path, array $header, array $body): HttpResponse;
    public function matchesPath(string $path, string $method="GET");
}
