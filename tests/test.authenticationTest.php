<?php
require_once('../loader.php');

load_dependencies('../');

use ch\makae\makaegallery\Authentication;
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

    public function testMatchingUrl()
    {
        $auth = new Authentication(
            new \ch\makae\makaegallery\SessionProviderMock(),
            $this->SALT,
            $this->users, [
            'domain.com/public' => 2
        ]);
        $auth->login("test", "123456");
        $this->assertTrue($auth->urlAllowed("domain.com/public"));
    }
}

?>
