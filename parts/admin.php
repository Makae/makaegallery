<?php

use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();
global $App;

$gallery_data = array();
$repository = $App->getGalleryRepository();
$security = $App->getSecurity();
foreach ($repository->getGalleries() as $gallery) {
    $images = Utils::mapImagesToArray($gallery->getImages(true, false));
    $gallery_data[] = array(
        'name' => $gallery->getIdentifier(),
        'images' => $images
    );
}
?>
<div class="admin" data-apiurl="<?= $App->getRestApi()->getUrl() ?>">
    <button id="minify-button" href="#" class="btn btn-light">Minify
        Galleries</button>
    <button id="clear-button" href="#" class="btn btn-danger">Clear Minified
        Images</button>

    <?php foreach ($gallery_data as $gallery): ?>
        <div class="gallery-wrapper">
            <h3><?= $gallery['name'] ?></h3>
            <div class="control-wrapper clearfix">
                <button data-gallery-id="<?= $gallery['name'] ?>" href="#"
                   class="gallery-button minify-gallery-button btn btn-light pull-left">Minify this gallery</button>
                <button data-gallery-id="<?= $gallery['name'] ?>" href="#"
                   class="gallery-button clear-gallery-button btn btn-danger pull-left">Clear this gallery</button>
            </div>
            <div class="upload-wrapper clearfix">
                <form>
                    <input type="file" multiple="multiple"
                           class="pull-left"
                           accept="<?= implode(" ", $repository->getAllowedImageTypes()) ?>"/>
                    <button data-gallery-id="<?= $gallery['name'] ?>"
                            data-nonce="<?= $security->createNonceToken($gallery['name']) ?>" type="button"
                            class="gallery-button upload-image-button btn btn-primary pull-left">Upload Images
                    </button>
                    <div class="message-wrapper error">
                    </div>
                    <div class="message-wrapper notification">
                    </div>
                </form>
            </div>
            <ul class="processing-progress" data-gallery="<?= $gallery['name'] ?>">
                <?php foreach ($gallery['images'] as $img): ?>
                    <li data-minify-img="<?= urlencode($img['id']) ?>"><?= explode('|', basename($img['id']))[1] ?><a
                                href="#"
                                class="manual-trigger btn btn-light"
                                data-minify-img="<?= urlencode($img['id']) ?>">Execute
                            now</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
<script src="<?= WWW_ASSETS ?>/js/admin.js"></script>
<link rel="stylesheet" href="<?= WWW_ASSETS ?>/css/admin.css">
