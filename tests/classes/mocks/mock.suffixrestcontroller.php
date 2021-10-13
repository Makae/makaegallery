<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\rest\HttpResponse;
use ch\makae\makaegallery\rest\controllers\SimpleRestController;

class SuffixSimpleRestController extends SimpleRestController
{

    private string $suffix;

    public function __construct(string $suffix, string $method = "GET", string $routePattern = "/", int $accessLevel = Authentication::ACCESS_LEVEL_ADMIN)
    {
        parent::__construct($method, $routePattern, $accessLevel);
        $this->suffix = $suffix;

    }

    public function handle(string $method, string $path, array $header, array $body): HttpResponse
    {
        return new HttpResponse($path . $this->suffix);
    }

}
