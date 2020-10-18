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

    public function get($key)
    {
        return $this->session[$key];
    }

    public function remove($key)
    {
        unset($this->session[$key]);
    }

    public function has($key)
    {
        return isset($this->session[$key]);
    }
}
