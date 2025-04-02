<?php

namespace App\Services;

use GdImage;
use App\Services\ImageConverter;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SaveConvertedImage
{
    private string $basePath;
    public function __construct(
        private readonly ImageConverter $imageConverter,
    ) {
        $this->basePath = 'uploads/';
    }

    /**
     * Save an image which has been converted to webp format on the server
     * @param UploadedFile $file - The file to save on the server
     * @param ?string $path - path if subfolder
     * @return string $filePath - The path to the saved file on the server
     */
    public function saveOneFile(UploadedFile $file, ?string $path = null): string
    {
        // create path name
        $path = $path ?? $this->basePath;

        $filePath = uniqid() . '_';
        $filePath .= pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filePath = str_replace(' ', '_', strtolower($filePath));
        $filePath .= '.webp';

        $filePath = $path . $filePath;

        // convert and save the image and destroy the image created (RAM)
        $image = $this->imageConverter->convertToWebp($file);
        imagewebp($image, $filePath, -1);
        imagedestroy($image);

        return $filePath;
    }
}
