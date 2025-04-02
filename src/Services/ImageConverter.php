<?php

namespace App\Services;

use InvalidArgumentException;
use GdImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageConverter
{
  /** 
   * Create an image file in webp format from an other image file
   * @param UploadedFile $file The file to convert
   * @return GdImage the converted file
   */
  public function convertToWebp(UploadedFile $file): GdImage
  {
    $mimeType = $file->getClientMimeType();

    $image = null;

    if ($mimeType === 'image/webp') {
      throw new InvalidArgumentException('Le fichier est déjà au format webp');
    }
    if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
      $image = imagecreatefromjpeg($file);
    }
    if ($mimeType === 'image/png') {
      $image = imagecreatefrompng($file);
    }
    if ($mimeType === 'image/gif') {
      $image = imagecreatefromgif($file);
    }
    if ($mimeType === 'image/bmp') {
      $image = imagecreatefrombmp($file);
    }

    // check if the image is valid
    if (!$image || !($image instanceof GdImage)) {
      throw new InvalidArgumentException('Le fichier n\'est pas une image supportée');
    }

    return $image;
  }



  /**
   * @param string|UploadedFile $data link (folder or file) to send
   */
  public function dataSend(
    string $data,
  ) {
    if (file_exists($data)) {

      if (is_file($data)) {
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($data) . "\"");
        readfile($data);
        unlink($data);
      }
    }
  }
}
