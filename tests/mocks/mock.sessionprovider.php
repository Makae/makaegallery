<?php

namespace ch\makae\makaegallery;

class SessionProviderMock implements ISessionProvider
{
    private $session;

    public function start()
    {
        $this->session = [];
    }

    public function end()
    {
        $this->session = [];
    }

    public function set($key, $value)
    {
        $this->session[$key] = $value;
    }

    public function remove($key)
    {
        unset($this->session[$key]);
    }

    public function getOrElse(string $identifier, $elseValue)
    {
        return $this->has($identifier) ? $this->get($identifier) : $elseValue;
    }

    public function has($key)
    {
        return isset($this->session[$key]);
    }

    public function get($key)
    {
        return $this->session[$key];
    }

    public function getAll() {
        return $this->session;
    }
}
