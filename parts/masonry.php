<?php

use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();

global $App;

$components = Utils::getUriComponents(Utils::getRequestUri());
$gallery_id = $components[1];
$gallery = $App->getGalleryRepository()->getGallery($gallery_id);
?>
<script src="<?= WWW_ASSETS ?>/js/libs/masonry.pkgd.js"></script>
<script src="<?= WWW_ASSETS ?>/js/libs/imagesloaded.pkgd.js"></script>
<script src="<?= WWW_ASSETS ?>/js/masonry.js"></script>
<link rel="stylesheet" href="<?= WWW_ASSETS ?>/css/masonry.css">

<div class="masonry"
     data-apiurl="<?= $App->getRestApi()->getUrl() ?>"
     data-galleryid="<?= $gallery->getIdentifier() ?>"
>
    <div class="grid">
        <div class="grid-sizer"></div>
    </div>
</div>
