<?php

use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();

global $App;

$components = Utils::getUriComponents(Utils::getRequestUri());
$gallery_id = $components[1];
$gallery = $App->getGalleryRepository()->getGallery($gallery_id);

$user = $App->getAuth()->getUser();
$repository = $App->getGalleryRepository()
?>
<script src="<?= WWW_ASSETS ?>/js/libs/masonry.pkgd.js"></script>
<script src="<?= WWW_ASSETS ?>/js/libs/imagesloaded.pkgd.js"></script>
<script src="<?= WWW_ASSETS ?>/js/masonry.js"></script>
<link rel="stylesheet" href="<?= WWW_ASSETS ?>/css/masonry.css">

<!-- Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">Foto hochladen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="photo-upload">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user">Name</label>
                        <input type="text" disabled="disabled" class="form-control" id="user" placeholder="Enter email"
                               value="<?= $user['name'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input type="text" class="form-control" id="title" placeholder="Fototitel angeben" value="">
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto</label>
                        <input type="file" multiple="multiple"
                               class="form-control"
                               accept="<?= implode(" ", $repository->getAllowedImageTypes()) ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="photo">Foto-Titel</label>
                        <input type="text" class="form-control" id="photo" placeholder="Fototitel angeben" value="">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Hochladen</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="masonry"
     data-apiurl="<?= $App->getRestApi()->getUrl() ?>"
     data-galleryid="<?= $gallery->getIdentifier() ?>"
>
    <div class="grid">
        <div class="grid-sizer"></div>
    </div>
</div>
