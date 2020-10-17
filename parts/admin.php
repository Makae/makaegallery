<?php
if (!defined('DOC_ROOT'))
    die();

global $MakaeGallery;

if (DOING_AJAX) {
    global $AJAX;
    $AJAX->admin();
}

$gallery_data = array();
foreach ($MakaeGallery->getGalleries() as $gallery) {
    $images = $gallery->getImageList(false, false);
    $gallery_data[] = array(
        'name' => $gallery->getIdentifier(),
        'images' => $images
    );
}
?>

<a id="minify-button" href="<?= WWW_BASE . "/admin?minify_galleries=1" ?>" class="btn btn-primary">Minify Galleries</a>

<a id="clear-button" href="<?= WWW_BASE . "/admin?clear_minified=1" ?>" class="btn btn-danger">Clear Minified Images</a>

<? foreach ($gallery_data as $gallery): ?>
    <div class="gallery-wrapper">
        <h3><?= $gallery['name'] ?></h3>
        <div class="control-wrapper clearfix">
            <a data-gallery-id="<?= $gallery['name'] ?>" href="<?= WWW_BASE . "/admin?minify_galleries=1" ?>"
               class="gallery-button minify-gallery-button btn btn-default pull-left">Minify this gallery</a>
            <a data-gallery-id="<?= $gallery['name'] ?>" href="<?= WWW_BASE . "/admin?clear_minified=1" ?>"
               class="gallery-button clear-gallery-button btn btn-danger pull-left">Clear this gallery</a>
        </div>
        <ul class="processing-progress" data-gallery="<?= $gallery['name'] ?>">
            <? foreach ($gallery['images'] as $img): ?>
                <li data-minify-img="<?= urlencode($img['imgid']) ?>"><?= basename($img['imgid']) ?><a href="#"
                                                                                                       class="manual-trigger btn btn-default"
                                                                                                       data-minify-img="<?= urlencode($img['imgid']) ?>">Execute
                        now</a>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? endforeach; ?>
<script src="<?= WWW_ASSETS ?>/js/admin.js"></script>
