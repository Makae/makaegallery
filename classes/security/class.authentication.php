<?php

namespace ch\makae\makaegallery\security;

use ch\makae\makaegallery\session\ISessionProvider;

class Authentication
{
    const ACCESS_LEVEL_ADMIN = 0;
    const ACCESS_LEVEL_USER = 1;
    const ACCESS_LEVEL_GUEST = 2;
    const ACCESS_LEVEL_PUBLIC = 100;

    private $salt;
    private $users;
    private $restrictions;

    public function __construct(ISessionProvider $sessionProvider, $salt, $users, $restrictions)
    {
        $this->sessionProvider = $sessionProvider;
        $this->salt = $salt;
        $this->users = $users;
        $this->restrictions = $restrictions;
    }

    public function routeAllowed($path)
    {
        $state = false;
        foreach ($this->restrictions as $r_path => $level) {
            if (strpos($path, $r_path) === 0) {
                $state = $this->hasAccessForLevel($level);
            }
        }
        return $state;
    }

    public function hasAccessForLevel($level)
    {
        if ($this->getUserLevel() <= $level) {
            return true;
        }
        return false;
    }

    public function isLoggedIn()
    {
        return $this->sessionProvider->has('user') && !is_null($this->sessionProvider->get('user'));
    }

    public function isAdmin()
    {
        return $this->isLoggedIn() && $this->sessionProvider->get('user')['level'] === Authentication::ACCESS_LEVEL_ADMIN;
    }

    public function getUserLevel()
    {
        if (!$this->isLoggedIn())
            return Authentication::ACCESS_LEVEL_PUBLIC;

        return $this->sessionProvider->get('user')['level'];
    }

    public function getUser() {
        if(!$this->sessionProvider->has('user')) {
            return null;
        }
        return $this->sessionProvider->get('user');
    }

    public function login($name, $password)
    {
        $password = md5($password . $this->salt);
        foreach ($this->users as $user) {
            if ($user['name'] == $name && $user['password'] === $password) {
                $this->sessionProvider->set('user', $user);
                return true;
            }
        }
        return false;
    }

    public function logout()
    {
        $this->sessionProvider->remove('user');
    }

    public function getUsers()
    {
        return $this->users;
    }

}
