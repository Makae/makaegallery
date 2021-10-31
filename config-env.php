<?php

use ch\makae\makaegallery\security\Authentication;

@define('CORS_ALLOWED_ORIGINS', 'http://localhost:4300');

@define('GALLERY_CONFIGURATION', serialize(array(
  'jum-photoshooting' => array(
    'title' => 'Photoshooting für Chärtli',
    'description' => 'Photoshooting für Iihladingscharte',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
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
  'jum-ceremony' => array(
    'title' => 'Zeremonie',
    'description' => '💑',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-photobox/converted/resized-1632565447507-800-1199-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-party' => array(
    'title' => 'Party',
    'description' => 'Apéro, Band und Party',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'cover' => WWW_GALLERY_ROOT . '/jum-photobox/converted/resized-1632565447507-800-1199-80.jpg',
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'photobox' => array(
    'title' => 'Bilder aus der Photobox',
    'description' => 'Die beschte Biuder het\'s gäh.',
    'ref_text' => 'Bilder von <b>Corinne Ritter</b>. Vielen Dank.',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'tenantId' => 'd9fff352-9c48-42b6-9e65-4e6a555a91d4'
  ),
  'public' => array(
    'title' => 'Bilder von der Hochzeit',
    'description' => 'So schön isch\'s gsi!',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'tenantId' => 'd9fff352-9c48-42b6-9e65-4e6a555a91d4'
  ),
  'private-tenant-gallery' => array(
    'title' => 'PRIVATE TENANT',
    'description' => 'sooo private',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'd9fff352-9c48-42b6-9e65-4e6a555a91d4'
  ),
  'other-private-tenant-gallery' => array(
    'title' => 'PRIVATE OTHER TENANT',
    'description' => 'sooo other private',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'fffff352-9c48-42b6-9e65-4e6a555a91d4'
  )
)));

@define('AUTH_USERS', serialize([
  [
    'name' => 'jum_admin',
    'password' => '23b43d87f5dac4cad8b2252ab8f77d82',
    'level' => Authentication::ACCESS_LEVEL_ADMIN,
  ],
  [
    'name' => 'jum_tenant_admin',
    'password' => '23b43d87f5dac4cad8b2252ab8f77d82',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ],
  [
    'name' => 'jum_guest',
    'password' => '23b43d87f5dac4cad8b2252ab8f77d82',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ],
  [
    'name' => 'jum_tenant_other_admin',
    'password' => '23b43d87f5dac4cad8b2252ab8f77d82',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'd9fff352-9c48-42b6-9e65-4e6a555a91d4'
  ],
  [
    'name' => 'photobox',
    'password' => '4041ed306863ddde6c9ebf2c2676edb7',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ],
  [
    'name' => 'besucher',
    'password' => '35b91af1d068598b2269aaf6cb56bfee',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ]
]));

@define('DEBUG', true);
