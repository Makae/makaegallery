<?php

namespace ch\makae\makaegallery\tests;

use ch\makae\makaegallery\Authentication;

class AuthenticationHelper
{
    private static $users = [
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
    private static $SALT = 'asdöfhöç2b4(&jwbj vyk sprog';

    public static function getAuthenticationMock($paths = [])
    {
        return new Authentication(
            new SessionProviderMock(),
            self::$SALT,
            self::$users,
            $paths);
    }
}
