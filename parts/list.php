<?php
if (!defined('DOC_ROOT'))
    die();
$has_gallery_output = false;
global $MakaeGallery;
global $Authentication;

?>
<div class="row">
    <? foreach ($MakaeGallery->getGalleries() as $gallery):
        if (!$Authentication->canAccess($gallery->getLevel()))
            continue;
        $has_gallery_output = true;
        ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <img src="<?= $gallery->getCover(); ?>" alt="<?= $gallery->getTitle(); ?>"/>
                <div class="caption">
                    <h3><?= $gallery->getTitle(); ?></h3>
                    <p><?= $gallery->getDescription(); ?></p>
                    <p><?= $gallery->getRefText(); ?></p>
                    <p><a href="<?= $gallery->getLink(); ?>" class="btn btn-primary" role="button">Jetzt anschauen</a>
                    </p>
                </div>
            </div>
        </div>
    <? endforeach; ?>
    <? if (!$has_gallery_output): ?>
        <p>Um die Gallerien zu betrachten musst Du dich anmelden:</p>
        <a href="<?= WWW_BASE . '/login' ?>" class="btn btn-primary">Zur Anmeldung</a>
    <? endif; ?>
</div>
