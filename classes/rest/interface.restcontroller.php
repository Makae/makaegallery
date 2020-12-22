<?php

namespace ch\makae\makaegallery\rest;

interface IRestController
{
    public function handle(string $method, string $path, array $header, array $body): HttpResponse;

    public function matchesPath(string $method, string $path): bool;

    public function getAccessLevel(string $method, string $path): int;
}
