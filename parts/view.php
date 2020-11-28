<?php

use ch\makae\makaegallery\Image;
use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();

global $App;

$components = Utils::getUriComponents(Utils::getRequestUri());
$gallery_id = $components[1];
$gallery = $App->getGalleryRepository()->getGallery($gallery_id);
$images = Utils::mapImagesToArray($gallery->getImages());
$columns = array_fill(0, GALLERY_COLUMNS, []);
$imgidx = 0;

for ($idx = 0; $idx < count($images); $idx++) {
    $image = &$images[$idx];
    $image['imgidx'] = $imgidx++;
    if ($idx < min(GALLERY_IMAGES_PER_LOAD, count($images))) {
        $column_idx = floor($idx % GALLERY_COLUMNS);
        $columns[$column_idx][] = $image;
    }
}
?>
<script src="<?= WWW_ASSETS ?>/js/event_buffer.js"></script>
<script src="<?= WWW_ASSETS ?>/js/gallery.js"></script>

<div class="row gallery" data-title="<?= $gallery->getTitle() ?>" data-imgidx="<?= $imgidx ?>"
     data-columns="<?= GALLERY_COLUMNS ?>" data-images="<?= urlencode(json_encode($images)); ?>"
     data-perload="<?= GALLERY_IMAGES_PER_LOAD ?>">
    <?php foreach ($columns as $idx => $col): ?>
        <div class="col-sm-4 column-<?= $idx + 1 ?>">
        </div>
    <?php endforeach; ?>
</div>
<div class="loadmore btn btn-primary">Mehr anzeigen</div>
<nav class="navbar navbar-default navbar-fixed-bottom">
    <div class="container clearfix">
        <div class="progressbar"></div>
        <div class="btn btn-primary load-prev-page pull-left">Vorherige Seite</div>
        <span class="pagexofy"><span class="pagex">X</span> von <span class="ofy">X</span></span>
        <div class="btn btn-primary load-next-page pull-right">Nächste Seite</div>
    </div>
</nav>
