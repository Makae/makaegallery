<?php


namespace ch\makae\makaegallery;


interface ISessionProvider
{
    public function start();

    public function end();

    public function set($key, $value);

    public function get($key);

    public function remove($key);

    public function has($string);
}
