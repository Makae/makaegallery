<?php

namespace ch\makae\makaegallery;

class Authentication
{
    const PUBLIC_USER_LEVEL = 100;

    private $sessionProvider;
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

    public function urlAllowed($url)
    {
        $state = false;
        foreach ($this->restrictions as $r_path => $level) {
            if (strpos($url, $r_path) === 0) {
                $state = $this->canAccess($level);
            }
        }

        return $state;
    }

    public function canAccess($level)
    {
        if ($this->userLevel() <= $level) {
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
        return $this->isLoggedIn() && $this->sessionProvider->get('user')['level'] === 0;
    }

    public function userLevel()
    {
        if (!$this->isLoggedIn())
            return Authentication::PUBLIC_USER_LEVEL;

        return $this->sessionProvider->get('user')['level'];
    }

    public function login($name, $password)
    {
        $password = md5($password . $this->salt);
        foreach ($this->users as $user) {
            if ($user['name'] == $name && $user['password'] === $password) {
                $this->sessionProvider->set('user', $user);
            }
        }
        return false;
    }

    public function logout()
    {
        $this->sessionProvider->remove('user');
    }

}
