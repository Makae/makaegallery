<?php

namespace ch\makae\makaegallery\rest;

class RestController implements IRestController
{
    public function handle(string $path)
    {
        return $path;
    }
}
