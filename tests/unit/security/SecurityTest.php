<?php

use ch\makae\makaegallery\Security;
use ch\makae\makaegallery\tests\SessionProviderMock;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function test_consumingNonceOnlyOnce_works()
    {
        $sessionProviderMock = new SessionProviderMock();
        $security = new Security($sessionProviderMock);
        $token = $security->createNonceToken("myNonce");
        $firstValidity = $security->validateNonceToken($token);
        $secondValidity = $security->validateNonceToken($token);

        $this->assertTrue($firstValidity);
        $this->assertFalse($secondValidity);
    }

    public function test_consumingNonceAfterValidity_fails()
    {
        $sessionProviderMock = new SessionProviderMock();

        $validityInterval = new DateInterval("PT1M");
        $validityInterval->invert = 1;
        $security = new Security($sessionProviderMock, $validityInterval);
        $token = $security->createNonceToken("myNonce");

        $this->assertFalse($security->validateNonceToken($token));
    }
}
