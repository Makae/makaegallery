<?php

namespace ch\makae\makaegallery;

class UploadHandler
{


    private IGalleryRepository $galleryRepository;

    public function __construct(IGalleryRepository $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function addUploadedImages(string $galleryId, array $files): UploadResult
    {
        $result = new UploadResult();
        foreach ($files as $file) {
            $result->addFileResult($this->addUploadedImage($galleryId, $file));
        }
        return $result;
    }

    private function addUploadedImage(string $galleryId, UploadedFile $file): UploadStatus
    {
        if ($file->getError() !== 0) {
            return new UploadStatus($file->getName(), 'upload_error');
        }

        $gallery = $this->galleryRepository->getGallery($galleryId);
        if (!$gallery) {
            return new UploadStatus($file->getName(), 'invalid_galleryid');
        }

        if (!$this->moveUploadedFile($file, $gallery->getFolder())) {
            return new UploadStatus($file->getName(), 'upload_error');
        }

        $image = $gallery->addImageByName($file->getName());
        if (is_null($image)) {
            return new UploadStatus($file->getName(), 'gallery_adding_error');
        }
        return new UploadStatus($file->getName());
    }


    public function getUploadedFiles(array $filesData): array
    {
        $fileData = is_array($filesData) ? $filesData : [$filesData];
        $files = [];
        for ($idx = 0; $idx < count($fileData['tmp_name']); $idx++) {
            $files[] = new UploadedFile(
                $fileData['name'][$idx],
                $fileData['tmp_name'][$idx],
                $fileData['size'][$idx],
                $fileData['error'][$idx]
            );
        }

        return $files;
    }

    private function moveUploadedFile(UploadedFile $file, string $targetFolder)
    {
        return move_uploaded_file($file->getTmpPath(), $targetFolder . DIRECTORY_SEPARATOR . basename($file->getName()));
    }

}
