<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\rest\RestController;

class SuffixRestController extends RestController
{

    private string $suffix;

    public function __construct(string $suffix, string $routePattern = "/")
    {
        parent::__construct($routePattern);
        $this->suffix = $suffix;

    }

    public function handle(string $path)
    {
        return $path . $this->suffix;
    }
}
