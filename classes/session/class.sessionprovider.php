<?php

namespace ch\makae\makaegallery\session;

class SessionProvider implements ISessionProvider
{
    public function start()
    {
        session_start();
    }

    public function end()
    {
        session_destroy();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return $_SESSION[$key];
    }

    public function remove($key) {
        unset($_SESSION[$key]);
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function getOrElse(string $identifier, $elseValue)
    {
        return $this->has($identifier) ? $this->get($identifier) : $elseValue;
    }
}
