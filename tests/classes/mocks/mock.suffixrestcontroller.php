<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\RestController;

class SuffixRestController extends RestController
{

    private string $suffix;

    public function __construct(string $suffix, string $routePattern = "/")
    {
        parent::__construct($routePattern);
        $this->suffix = $suffix;

    }

    public function handle(string $path, array $header, array $body): HttpResponse
    {
        return new HttpResponse($path . $this->suffix);
    }
}
