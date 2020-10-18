<?php
require_once('../loader.php');

load_dependencies('../');
require_once('mocks/mock.sessionprovider.php');

use ch\makae\makaegallery\Authentication;
use ch\makae\makaegallery\SessionProviderMock;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    private $users = [
        [
            'name' => 'admin',
            'password' => 'e1de572330748174d8f1b46eb3162fca',
            'level' => 0,
        ],
        [
            'name' => 'user',
            'password' => 'e1de572330748174d8f1b46eb3162fca',
            'level' => 1,
        ],
        [
            'name' => 'guest',
            'password' => 'e1de572330748174d8f1b46eb3162fca',
            'level' => 2,
        ]
    ];
    private $SALT = 'asdöfhöç2b4(&jwbj vyk sprog';

    private function getAuthenticationMock($paths = [])
    {
        return new Authentication(
            new SessionProviderMock(),
            $this->SALT,
            $this->users, $paths);
    }

    public function test_login_validCredentials_isLoggedIn()
    {
        $auth = $this->getAuthenticationMock([]);
        $auth->login("admin", "123456");
        $this->assertTrue($auth->isLoggedIn());
    }

    public function test_login_invalidCredentials_isNotLoggedIn()
    {
        $auth = $this->getAuthenticationMock([]);
        $auth->login("admin", "WRONG");
        $this->assertFalse($auth->isLoggedIn());
    }

    public function test_logout_userLogout_loggsUserOut()
    {
        $auth = $this->getAuthenticationMock([]);
        $auth->login("admin", "123456");
        $this->assertTrue($auth->isLoggedIn());
        $auth->logout();
        $this->assertFalse($auth->isLoggedIn());
    }

    public function test_login_loginTwice_loggedInAsLastUser()
    {
        $auth = $this->getAuthenticationMock([]);
        $auth->login("admin", "123456");
        $auth->login("user", "123456");
        $this->assertEquals(Authentication::USER_LEVEL_USER, $auth->getUserLevel());
        $this->assertEquals("user", $auth->getUser()['name']);
    }

    public function test_getUserLevel_NotLoggedIN_hasPublicUserLevel()
    {
        $auth = $this->getAuthenticationMock();
        $this->assertEquals(Authentication::USER_LEVEL_PUBLIC, $auth->getUserLevel());
    }

    public function test_canAccess_asAdmin_allLevelAccessible() {
        $auth = $this->getAuthenticationMock();
        $auth->login("admin", "123456");
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_PUBLIC));
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_GUEST));
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_USER));
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_ADMIN));
    }

    public function test_canAccess_asGuest_guestOrBelowAccessible() {
        $auth = $this->getAuthenticationMock();
        $auth->login("guest", "123456");
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_PUBLIC));
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_GUEST));
        $this->assertFalse($auth->canAccess(Authentication::USER_LEVEL_USER));
        $this->assertFalse($auth->canAccess(Authentication::USER_LEVEL_ADMIN));
    }

    public function test_canAccess_notLoggedIn_onlyPublicAccessible() {
        $auth = $this->getAuthenticationMock();
        $this->assertTrue($auth->canAccess(Authentication::USER_LEVEL_PUBLIC));
        $this->assertFalse($auth->canAccess(Authentication::USER_LEVEL_GUEST));
        $this->assertFalse($auth->canAccess(Authentication::USER_LEVEL_USER));
        $this->assertFalse($auth->canAccess(Authentication::USER_LEVEL_ADMIN));
    }



}

?>
