<?php

class Authentication {
    use Singleton;

    private $users;
    private $restrictions;

    public function __construct() {}

    public function setUsers($users) {
        $this->users = $users;
    }

    public function setRestrictions($restrictions) {
        $this->restrictions = $restrictions;
    }

    public function urlAllowed($url) {
        $state = true;
        foreach($this->restrictions as $r_path => $level) {
            if(strpos($r_path, $url) === 0) {
                $state = $this->canAccess($level);
            }
        }
        
        return $state;
    }

    public function canAccess($level) {
        if($level == 0){
           return true;
        }
        if($level == 1 && $this->isLoggedIn()) {
           return true;
        }
        if($level == 2 && $this->isAdmin()) {
           return true;
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user']) && !is_null($_SESSION['user']);
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['user']['level'] === 2;
    }

    public function login($name, $password) {
        $password = md5($password . SALT);
        foreach($this->users as $user) {
            if($user['name'] == $name && $user['password'] === $password) {
                $_SESSION['user'] = $user;
                return true;
            }
        }
        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
    }

}