<?php

namespace ch\makae\makaegallery\rest;

interface IRestController
{
    public function handle(string $path);
    public function matchesPath(string $path);
}
