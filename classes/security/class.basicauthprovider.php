<?php

namespace ch\makae\makaegallery\security;

class BasicAuthProvider implements IAuthProvider
{

  private array $users;
  private string $salt;

  public function __construct(array $users, string $salt)
  {
    $this->salt = $salt;
    $this->users = $users;
  }

  public function getCurrentUser()
  {
    if (!$this->isAuthenticated()) {
      return null;
    }
    return $this->findUserById($this->getCredentials()['id']);
  }

  public function isAuthenticated(): bool
  {
    $credentials = $this->getCredentials();

    if (!$credentials) {
      return false;
    }
    return $this->areCredentialsValid($credentials['id'], $credentials['password']);
  }

  private function getCredentials(): ?array
  {
    global $_SERVER;
    $user = $_SERVER['PHP_AUTH_USER'] ?? null;
    $password = $_SERVER['PHP_AUTH_PW'] ?? null;
    return $user && $password ? ['id' => $user, 'password' => $password] : null;
  }

  private function areCredentialsValid($id, $password): bool
  {
    $user = $this->findUserById($id);
    $password = md5($password . $this->salt);

    if ($user && $user['password'] === $password) {
      return true;
    }
    return false;
  }

  private function findUserById($id): ?array
  {
    foreach ($this->users as $user) {
      if ($user['name'] === $id) {
        return $user;
      }
    }
    return null;
  }

  public function getAllUsers()
  {
    return $this->users;
  }
}
