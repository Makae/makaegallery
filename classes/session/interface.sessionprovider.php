<?php


namespace ch\makae\makaegallery\session;


interface ISessionProvider
{
    public function start();

    public function end();

    public function set($key, $value);

    public function get($key);

    public function remove($key);

    public function has($string);

    public function getOrElse(string $identifier, $elseValue);
}
