<?php

use ch\makae\makaegallery\Authentication;

@define('GALLERY_CONFIGURATION', serialize(array(
    'photobox' => array(
        'title' => 'Bilder aus der Photobox',
        'description' => 'Die beschte Biuder het\'s gäh.',
        'ref_text' => 'Bilder von <b>Corinne Ritter</b>. Vielen Dank.',
        'level' => 1
    ),
    'public' => array(
        'title' => 'Bilder von der Hochzeit',
        'description' => 'So schön isch\'s gsi!',
        'level' => 2
    )
)));

@define('AUTH_USERS', serialize(array(
    array(
        'name' => 'radmin',
        'password' => '1f5153edc921f1eee2e7916fdf98f0c6',
        'level' => Authentication::ACCESS_LEVEL_ADMIN,
    ),
    array(
        'name' => 'jum_admin',
        'password' => '23b43d87f5dac4cad8b2252ab8f77d82',
        'level' => Authentication::ACCESS_LEVEL_ADMIN,
    ),
    array(
        'name' => 'photobox',
        'password' => '4041ed306863ddde6c9ebf2c2676edb7',
        'level' => Authentication::ACCESS_LEVEL_USER,
    ),
    array(
        'name' => 'besucher',
        'password' => '35b91af1d068598b2269aaf6cb56bfee',
        'level' => Authentication::ACCESS_LEVEL_GUEST,
    )
)));
