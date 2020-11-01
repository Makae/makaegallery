<?php

namespace ch\makae\makaegallery;

use DateInterval;
use DateTime;

class Security
{
    private const SALT = "aü0-thaü3gnyv(6e°46?";
    private \DateInterval $validityInterval;
    private ISessionProvider $sessionProvider;

    public function __construct(ISessionProvider $sessionProvider, ?DateInterval $validityInterval=null)
    {
        $this->sessionProvider = $sessionProvider;
        $this->validityInterval = is_null($validityInterval) ? new \DateInterval("PT5M") : $validityInterval;
    }

    public function createNonceToken(string $key): string
    {
        $validUntil = $this->now()->add($this->validityInterval);
        $token = new NonceToken($this->generateTokenId($key), $validUntil);
        $this->sessionProvider->set(
            'nonce-' . $token->getId(),
            $token
        );
        return $token->getId();
    }



    private function now(): DateTime
    {
        return new DateTime();
    }

    public function validateNonceToken(string $identifier): bool
    {
        $token = $this->getToken($identifier);
        $this->clearToken($identifier);
        if ($token === null) return false;

        return $this->now() <= $token->getValidUntil();
    }

    private function getToken(string $identifier): ?NonceToken
    {
        return $this->sessionProvider->getOrElse('nonce-' . $identifier, null);
    }

    private function clearToken(string $identifier): void
    {
        $this->sessionProvider->remove($identifier);
    }

    private function generateTokenId(string $key)
    {
        return md5($key . self::SALT);
    }


}
