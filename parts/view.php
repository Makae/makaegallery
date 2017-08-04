<?php
if(!defined('DOC_ROOT'))
    die();
$components = Utils::getUriComponents();
$gallery_id = $components[1];
$gallery = Utils::getGallery($gallery_id);
$images = $gallery->getPublicImageList(true, true);

$columns = array_fill(0, GALLERY_COLUMNS, []);
$imgidx = 0;

for($idx = 0; $idx < count($images); $idx++) {
    $image = &$images[$idx];
    $image['imgidx'] = $imgidx++;
    if( $idx < min(GALLERY_IMAGES_PER_LOAD, count($images))) {
        $column_idx = floor($idx % GALLERY_COLUMNS);
        $columns[$column_idx][] = $image;
        $image['doload'] = true;
    }
}
?>
<script src="<?= WWW_ASSETS ?>js/event_buffer.js"></script>
<script src="<?= WWW_ASSETS ?>js/gallery.js"></script>

<div class="row gallery" data-title="<?= $gallery->getTitle() ?>" data-imgidx="<?= $imgidx ?>" data-columns="<?= GALLERY_COLUMNS ?>" data-images="<?= urlencode(json_encode($images)); ?>" data-perload="<?= GALLERY_IMAGES_PER_LOAD ?>" >
<? foreach($columns as $idx => $col): ?>
    <div class="col-sm-4 column-<?= $idx+1 ?>">
<? /* ?>
        <? foreach($col as $idx => $image): ?>
        <? $padding = ($image['dimensions']['height'] / $image['dimensions']['width']) * 100 ?>
        <div class="image-holder loaded" style="padding-bottom: <?= $padding ?>%;" data-imgidx="<?= $image['imgidx'] ?>">
            <span class="loader loader-black"></span>
            <img src="<?= $image['thumbnail_url'] ?>" data-modalimage="<?= $image['optimized_url'] ?>" data-bigimage="<?= $image['original_url'] ?>" alt="<?= $gallery->getTitle(); ?>" />
        </div>
        <? endforeach; ?>
<?php */ ?>
    </div>
<? endforeach; ?>
</div>
<div class="loadmore btn btn-primary">Mehr anzeigen</div>