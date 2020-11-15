<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\rest\IRestController;

class SuffixRestController implements IRestController
{

    public function handle(string $path)
    {
        return $path . "-suffix";
    }
}
