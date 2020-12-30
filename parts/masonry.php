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

<div class="modal fade" id="uploadPhotoModal" tabindex="-1" role="dialog" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">Foto hochladen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="photo-upload">
                    <div class="form-group">
                        <label for="user">Name</label>
                        <input type="text" disabled="disabled" class="form-control" id="user" placeholder="Enter email" value="<?= $user['name'] ?>">
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
                        <input type="text" class="form-control" id="photo" placeholder="Fototitel angeben" value="">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
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
