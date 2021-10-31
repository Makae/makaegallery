<?php

use ch\makae\makaegallery\security\Authentication;

@define('CORS_ALLOWED_ORIGINS', 'http://localhost:4300');

@define('GALLERY_CONFIGURATION', serialize(array(
  'photobox' => array(
    'title' => 'Bilder aus der Photobox',
    'description' => 'Die beschte Biuder het\'s gäh.',
    'ref_text' => 'Bilder von <b>Corinne Ritter</b>. Vielen Dank.',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'jum-photobox' => array(
    'title' => 'Biuder us dr Photibox',
    'description' => 'Die beschte Biuder het\'s gäh.',
    'level' => Authentication::ACCESS_LEVEL_USER,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'public' => array(
    'title' => 'Bilder von der Hochzeit',
    'description' => 'So schön isch\'s gsi!',
    'level' => Authentication::ACCESS_LEVEL_GUEST,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'private-tenant-gallery' => array(
    'title' => 'PRIVATE TENANT',
    'description' => 'sooo private',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'a84ce876-a1d9-4695-8b90-321c0ccf0db8'
  ),
  'other-private-tenant-gallery' => array(
    'title' => 'PRIVATE OTHER TENANT',
    'description' => 'sooo other private',
    'level' => Authentication::ACCESS_LEVEL_TENANT_ADMIN,
    'tenantId' => 'd9fff352-9c48-42b6-9e65-4e6a555a91d4'
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
