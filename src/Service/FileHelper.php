<?php

namespace App\Service;

use finfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileHelper
{

    /**
     * Function for save file in directory
     *
     * @param UploadedFile $file
     * @param string $targetDirectory
     * @return string|null
     */
    public static function saveFile(UploadedFile $file, string $targetDirectory)
    {
        $slugger=new AsciiSlugger();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetDirectory, $newFilename);

            return "$targetDirectory/$newFilename";
        } catch (\Exception $e) {
            throw new \Exception(500, 'Failed to upload file.');
        }

    }

    public static function saveFileFromUrl(string $url, string $targetDirectory)
    {
        $imageData =file_get_contents($url);
        if ($imageData === false) {
            throw new \Exception(500, 'Failed to upload file.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];
        $extension = $mimeToExtension[$mimeType] ?? 'bin';
        $filename = uniqid() . '.' . $extension;

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
        $filePath = "$targetDirectory/$filename";
        file_put_contents($filePath, $imageData);

        return $filePath;
    }

    /**
     * Delete file if exists
     *
     * @param string $filePath
     * @return bool
     */
    public static function deleteFile(string $filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

}