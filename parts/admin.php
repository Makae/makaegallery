<?php

use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();

global $App;

if (DOING_AJAX) {
    $App->getAjax()->admin();
    return;
}

$gallery_data = array();
foreach ($App->getMakaeGallery()->getGalleries() as $gallery) {
    $images = Utils::mapImagesToArray($gallery->getImages(true, false));
    $gallery_data[] = array(
        'name' => $gallery->getIdentifier(),
        'images' => $images
    );
}
?>

<a id="minify-button" href="<?= WWW_BASE . "/admin?minify_galleries=1" ?>" class="btn btn-primary">Minify Galleries</a>

<a id="clear-button" href="<?= WWW_BASE . "/admin?clear_minified=1" ?>" class="btn btn-danger">Clear Minified Images</a>

<?php foreach ($gallery_data as $gallery): ?>
    <div class="gallery-wrapper">
        <h3><?= $gallery['name'] ?></h3>
        <div class="control-wrapper clearfix">
            <a data-gallery-id="<?= $gallery['name'] ?>" href="<?= WWW_BASE . "/admin?minify_galleries=1" ?>"
               class="gallery-button minify-gallery-button btn btn-default pull-left">Minify this gallery</a>
            <a data-gallery-id="<?= $gallery['name'] ?>" href="<?= WWW_BASE . "/admin?clear_minified=1" ?>"
               class="gallery-button clear-gallery-button btn btn-danger pull-left">Clear this gallery</a>
        </div>
        <ul class="processing-progress" data-gallery="<?= $gallery['name'] ?>">
            <?php foreach ($gallery['images'] as $img): ?>
                <li data-minify-img="<?= urlencode($img['id']) ?>"><?= basename($img['id']) ?><a href="#"
                                                                                                       class="manual-trigger btn btn-default"
                                                                                                       data-minify-img="<?= urlencode($img['id']) ?>">Execute
                        now</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
<script src="<?= WWW_ASSETS ?>/js/admin.js"></script>
