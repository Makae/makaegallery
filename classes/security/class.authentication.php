<?php

namespace ch\makae\makaegallery\security;

class Authentication
{
  const ACCESS_LEVEL_ADMIN = 0;
  const ACCESS_LEVEL_USER = 1;
  const ACCESS_LEVEL_GUEST = 2;
  const ACCESS_LEVEL_RESTRICTED = 80;
  const ACCESS_LEVEL_PUBLIC = 100;

  private IAuthProvider $authProvider;

  public function __construct(IAuthProvider $authProvider)
  {
    $this->authProvider = $authProvider;
  }

  public function hasAccessForLevel($level): bool
  {
    if ($this->getUserLevel() <= $level) {
      return true;
    }
    return false;
  }

  public function isAuthenticated()
  {
    return $this->authProvider->isAuthenticated();
  }

  public function isAdmin()
  {
    return $this->hasAccessForLevel(Authentication::ACCESS_LEVEL_ADMIN);
  }

  public function getTenantId(): string {
    return $this->getUser()['tenantId'];
  }

  public function getUserLevel()
  {
    if (!$this->isAuthenticated()) {
      return Authentication::ACCESS_LEVEL_PUBLIC;
    }

    return $this->authProvider->getCurrentUser()['level'];
  }

  public function getUser()
  {
    return $this->getUser();
  }

  public function getUsers()
  {
    return $this->authProvider->getAllUsers();
  }

}
