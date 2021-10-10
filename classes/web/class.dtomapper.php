<?php


namespace ch\makae\makaegallery\web;


use ch\makae\makaegallery\Image;
use ch\makae\makaegallery\PublicGallery;

abstract class DtoMapper
{
    public static function mapGalleryToDto(PublicGallery $gallery): array
    {
        return [
            'identifier' => $gallery->getIdentifier(),
            'title' => $gallery->getTitle(),
            'images' => DtoMapper::mapImageListToDto($gallery->getImages())
        ];
    }

    private static function mapImageListToDto(?array $imageList): array
    {
        $images = [];
        if (is_null($imageList)) {
            return $images;
        }

        foreach ($imageList as $image) {
            $images[] = DtoMapper::mapImageToDto($image);
        }

        return $images;
    }

    public static function mapImageToDto(Image $image): array
    {
        return [
            'id' => $image->getIdentifier(),
            'thumbnail_url' => $image->getThumbnailUrl(),
            'optimized_url' => $image->getOptimizedUrl(),
            'original_url' => $image->getOriginalUrl(),
            'dimensions' => ['width' => $image->getWidth(), 'height' => $image->getHeight()]
        ];
    }

  public static function mapGalleryArrayToDto(array $galleries): array
  {
    $array_map = [];
    foreach ($galleries as $key => $value) {
      $array_map[$key] = DtoMapper::mapGalleryToDto($galleries[$key]);
    }
    return $array_map;
  }

}
