<?php
if (!defined('DOC_ROOT'))
    die();
$has_gallery_output = false;
global $App;

$galleries = $App->getGalleryRepository()->getGalleries();
?>
<div class="row">
    <?php foreach ($galleries as $gallery):
        if (!$App->getAuth()->hasAccessForLevel($gallery->getLevel()))
            continue;
        $has_gallery_output = true;
        ?>
        <div class="card col-sm-6 col-md-4"" style="width: 18rem;">
            <img src="<?= $gallery->getCover(); ?>" class="card-img-top" alt="<?= $gallery->getTitle(); ?>">
            <div class="card-body">
                <h5 class="card-title"><?= $gallery->getTitle(); ?></h5>
                <p class="card-text">
                    <span><?= $gallery->getDescription(); ?></span>
                    <span><?= $gallery->getRefText(); ?></span>
                </p>
                <a href="<?= $gallery->getLink(); ?>" class="btn btn-primary">Jetzt anschauen</a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (count($galleries) === 0): ?>
        <p>Es sind keine Gallerien vorhanden. Bitte lege welche an.</p>
    <?php endif; ?>
    <?php if (!$has_gallery_output && count($galleries) !== 0): ?>
        <p>Um die Gallerien zu betrachten musst Du dich anmelden:</p>
        <a href="<?= WWW_BASE . '/login' ?>" class="btn btn-primary">Zur Anmeldung</a>
    <?php endif; ?>
</div>
