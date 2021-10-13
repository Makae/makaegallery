<?php


namespace ch\makae\makaegallery\security;


interface IAuthProvider
{
    public function isAuthenticated();

  public function getAllUsers();

}
