<?php

namespace ch\makae\makaegallery\rest;

interface IRestController
{
    public function handle(string $path, array $header, array $body): HttpResponse;
    public function matchesPath(string $path);
}
