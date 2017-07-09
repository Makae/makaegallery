<?php

class Processor {
    private $name;
    private $config;

    public function __construct($name, $config) {
        $this->name = $name;
        $this->config = $config;
    }

    public function process($image) {
        return Convert::instance()->resize($image['original_path'], $this->config);
    }
}