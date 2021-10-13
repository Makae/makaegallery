<?php

namespace ch\makae\makaegallery\security;

class Authentication
{
  const ACCESS_LEVEL_ADMIN = 5;
  const ACCESS_LEVEL_TENANT_ADMIN = 10;
  const ACCESS_LEVEL_USER = 20;
  const ACCESS_LEVEL_GUEST = 30;
  const ACCESS_LEVEL_RESTRICTED = 90;
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


  public function isTenantAdmin()
  {
    return $this->hasAccessForLevel(Authentication::ACCESS_LEVEL_ADMIN);
  }

  public function isSuperAdmin()
  {
    return $this->hasAccessForLevel(self::ACCESS_LEVEL_TENANT_ADMIN);
  }

}
