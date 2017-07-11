<?php
if(!defined('DOC_ROOT'))
    die();

global $galleries;

if(DOING_AJAX) {
    AJAX::admin();
}

$gallery_data = array();
foreach($galleries as $gallery) {
    $images = $gallery->getImageList(false);
    $gallery_data[] = array(
        'name' => $gallery->getIdentifier(),
        'images' => $images
    );
}
?>

<a id="minify-button" href="<?= WWW_ROOT. "admin?minify_galleries=1" ?>" class="btn btn-primary" >Minify Galleries</a>
<a id="clear-button" href="<?= WWW_ROOT. "admin?clear_minified=1" ?>" class="btn btn-danger" >Clear Minified Images</a>

<? foreach($gallery_data as $gallery): ?>
<h3><?= $gallery['name'] ?></h3>
<ul class="processing-progress">
    <? foreach($gallery['images'] as $img): ?>
    <li data-minify-img="<?= urlencode($img['imgid']) ?>"><?= $gallery['name'] . "::". basename($img['imgid']) ?><a href="#" class="manual-trigger btn btn-default" data-minify-img="<?= urlencode($img['imgid']) ?>">Execute now</a>
    </li> 
    <? endforeach; ?>
</ul>
<? endforeach; ?>
<script src="<?= WWW_ASSETS ?>js/admin.js"></script>