<?php

@define('GALLERY_CONFIGURATION', serialize(array(
    'photobox' => array(
        'title' => 'Bilder aus der Photobox',
        'description' => 'Die beschte Biuder het\'s gäh',
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
        'level' => 0,
    ),
    array(
        'name' => 'photobox',
        'password' => '4041ed306863ddde6c9ebf2c2676edb7',
        'level' => 1,
    ),
    array(
        'name' => 'besucher',
        'password' => '35b91af1d068598b2269aaf6cb56bfee',
        'level' => 2,
    )
)));
