<?php

use ch\makae\makaegallery\security\Authentication;

@define('CORS_ALLOWED_ORIGINS', 'http://localhost:4300');

@define('GALLERY_CONFIGURATION', serialize(array(
  'jum-apero' => array(
    'title' => 'Apéro',
    'description' => 'Vor und nach der Zeremonie',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-apero/converted/resized-0-200-1200-800-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-brautpaar' => array(
    'title' => 'Photos vom Brautpaar',
    'description' => '♥',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'cover' => WWW_GALLERY_ROOT . '/jum-brautpaar/converted/resized-0-447-800-1200-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-ceremony' => array(
    'title' => 'Zeremonie',
    'description' => '💏',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-ceremony/converted/resized-0-272-1200-800-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-party' => array(
    'title' => 'Party',
    'description' => 'Aues ab em Znacht',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-party/converted/resized-0-613-975-650-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-photoshooting' => array(
    'title' => 'Photoshooting für Chärtli',
    'description' => 'Photoshooting für Iihladingscharte',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'cover' => WWW_GALLERY_ROOT . '/jum-photoshooting/converted/resized-52%20-%20052-1200-800-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-photobox' => array(
    'title' => 'Biuder us dr Photibox',
    'description' => 'Hei e gueti party gha 😉',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-photobox/converted/resized-1632565447507-800-1199-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
)));

@define('AUTH_USERS', serialize([
  [
    'name' => 'jum_partygast',
    'password' => 'ea5fa4bcab075237ff67660bf626f071',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ]
]));

@define('DEBUG', false);
