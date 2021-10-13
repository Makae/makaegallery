<?php

use ch\makae\makaegallery\security\Authentication;
use ch\makae\makaegallery\tests\AuthenticationHelper;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{

    public function test_login_validCredentials_isLoggedIn()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("admin", "123456");
        $this->assertTrue($auth->isAuthenticated());
    }


    public function test_login_invalidCredentials_isNotLoggedIn()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("admin", "WRONG");
        $this->assertFalse($auth->isAuthenticated());
    }

    public function test_logout_userLogout_loggsUserOut()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("admin", "123456");
        $this->assertTrue($auth->isAuthenticated());
        $auth->logout();
        $this->assertFalse($auth->isAuthenticated());
    }

    public function test_login_loginTwice_loggedInAsLastUser()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("admin", "123456");
        $auth->login("user", "123456");
        $this->assertEquals(Authentication::ACCESS_LEVEL_USER, $auth->getUserLevel());
        $this->assertEquals("user", $auth->getUser()['name']);
    }

    public function test_getUserLevel_NotLoggedIN_hasPublicUserLevel()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $this->assertEquals(Authentication::ACCESS_LEVEL_PUBLIC, $auth->getUserLevel());
    }

    public function test_canAccess_asAdmin_allLevelAccessible()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("admin", "123456");
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_PUBLIC));
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_GUEST));
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_USER));
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_TENANT_ADMIN));
    }

    public function test_canAccess_asGuest_guestOrBelowAccessible()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $auth->login("guest", "123456");
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_PUBLIC));
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_GUEST));
        $this->assertFalse($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_USER));
        $this->assertFalse($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_TENANT_ADMIN));
    }

    public function test_canAccess_notLoggedIn_onlyPublicAccessible()
    {
        $auth = AuthenticationHelper::getAuthenticationMock();
        $this->assertTrue($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_PUBLIC));
        $this->assertFalse($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_GUEST));
        $this->assertFalse($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_USER));
        $this->assertFalse($auth->hasAccessForLevel(Authentication::ACCESS_LEVEL_TENANT_ADMIN));
    }


}

?>
