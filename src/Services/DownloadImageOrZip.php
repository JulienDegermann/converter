<?php

namespace App\Services;

use ZipArchive;
use InvalidArgumentException;
use App\Services\SaveConvertedImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


final class DownloadImageOrZip
{
    private string $basePath;
    public function __construct(
        private readonly SaveConvertedImage $saveConvertedImage,
        // private readonly FlashBagInterface $flashBag
    ) {
        $this->basePath = 'uploads/';
    }

    /**
     * Generate path of files to download
     * @param UploadedFile[] $datas link (folder or file) to send
     * @return string The path to the saved file or ZIP
     */
    public function downloadFiles(array $datas): string
    {
        $path = $this->basePath;

        if (count($datas) === 0) {
            throw new InvalidArgumentException('Aucun fichier à télécharger');
        }

        if (count($datas) > 1) {
            $dirName = uniqid() . '_webp_images/';
            $path .= $dirName;
            mkdir($path, 0777, true);
        }
        foreach ($datas as $file) {
            if (!$file instanceof UploadedFile) {
                throw new InvalidArgumentException('Le fichier n\'est pas une image supportée');
            }

            $filePath = $this->saveConvertedImage->saveOneFile($file, $path);

            if (count($datas) === 1) {
                $path = $filePath;
            }
        }

        if (!is_dir($path) && !is_file($path)) {
            throw new InvalidArgumentException('Le fichier n\'existe pas');
        }
        if (is_dir($path)) {
            $zip = new ZipArchive();
            $zipName = $path . uniqid() . '_webp_images.zip';

            $zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $files = scandir($path);
            foreach ($files as $file) {
                if (is_file($path . $file) && file_exists($path . $file)) {
                    $zip->addFile($path . $file, $file);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipName) . '"');
            header('Content-Length: ' . filesize($zipName));
            readfile($zipName);
            unlink($zipName);
        } elseif (file_exists($path) && is_file($path)) {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($path) . "\"");
            readfile($path);
        }


        // remove files
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    unlink($path . $file);
                }
            }
            rmdir($path);
        }
        if (file_exists($path) && is_file($path)) {
            unlink($path);
        }
        return 'Téléchargement terminé';
    }
}
