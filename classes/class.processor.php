<?php
namespace ch\makae\makaegallery;

class Processor {
    private $name;
    private $config;
    private $converter;

    public function __construct($converter, $name, $config) {
        $this->converter = $converter;
        $this->name = $name;
        $this->config = $config;
    }

    public function process($image) {
        return $this->converter->resize($image['original_path'], $this->config);
    }
}
