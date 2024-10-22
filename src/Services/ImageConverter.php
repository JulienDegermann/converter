<?php

namespace App\Services;

use Exception;
use GdImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class ImageConverter
{

  /**
   * @param UploadedFile $file The file to convert
   * @return string The mime type of the file
   */
  public function getMimeType(UploadedFile $file): string
  {
    return $file->getClientMimeType();
  }

  /**
   * @param UploadedFile $file The jpeg image to convert
   * @return string The image resource
   */
  public function jpegToWebp(UploadedFile $file): GdImage
  {
    return imagecreatefromjpeg($file);
  }

  /**
   * @param UploadedFile $file The png image to convert
   * @return string The image resource
   */
  public function pngToWebp(UploadedFile $file): GdImage
  {
    return imagecreatefrompng($file);
  }

  /**
   * @param UploadedFile $file The gif image to convert
   * @return string The image resource
   */
  public function gifToWebp(UploadedFile $file): GdImage
  {
    return imagecreatefromgif($file);
  }

  /**
   * @param UploadedFile $file The bmp image to convert
   * @return string The image resource GdImage
   */
  public function bmpToWebp(UploadedFile $file): GdImage
  {
    return imagecreatefrombmp($file);
  }

  public function createFolder(): string
  {
    $folder = 'webp' . uniqid();
    mkdir($folder);
    return $folder;
  }


  /** 
   * @param UploadedFile $file The file to convert
   * @param bool $mutliple If the several files are uploaded : make a folder or zip
   * @return string The webp image and save it on server
   */
  public function convertToWebp(UploadedFile $file, ?string $folder = "./uploads/"): string
  {
    $mimeType = $this->getMimeType($file);

    $image = null;

    if ($mimeType === 'image/jpeg') {
      $image = $this->jpegToWebp($file);
    } else if ($mimeType === 'image/png') {
      $image = $this->pngToWebp($file);
    } else if ($mimeType === 'image/gif') {
      $image = $this->gifToWebp($file);
    } else if ($mimeType === 'image/bmp') {
      $image = $this->bmpToWebp($file);
    } else {
      throw new \Exception('Le fichier n\'est pas une image supportée');
    }



    // save the image with uniqid and destroy the image created
    if ($image instanceof GdImage) {
      $newFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
      // save on server

      // dd($folder . $newFileName);
      imagewebp($image, $folder . $newFileName, -1);
      // destroy created image (RAM)
      imagedestroy($image);

      return $newFileName;

      // if (file_exists($newFileName)) {
      //   // dd('ok');

      //   // unlink($newFileName); // Supprime le fichier

      //   $file_url = $newFileName;
      //   header('Content-Type: application/octet-stream');
      //   header("Content-Transfer-Encoding: Binary");
      //   header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
      //   readfile($file_url);
      //   unlink($file_url);
      //   return;
      // }
    }
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
      // if (is_dir($data)) {
      //   // dd('dossier');

      //   // create a zip file and send it
      //   $zip = new \ZipArchive();


      //   $zipFileName = $data . '.zip';
      //   dd($zipFileName);
      //   if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      //     throw new Exception('Impossible de créer le fichier zip');
      //   }


      // return $zip;
      // }
    }
  }
}
