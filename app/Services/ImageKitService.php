<?php

namespace App\Services;

use ImageKit\ImageKit;

class ImageKitService
{
    protected $imageKit;

    public function __construct()
    {
        $this->imageKit = new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }

    /**
     * Upload image to ImageKit
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fileName
     * @param string $folder
     * @return object
     */
    public function upload($file, $fileName, $folder = 'products')
    {
        return $this->imageKit->uploadFiles([
            'file' => base64_encode(file_get_contents($file->path())),
            'fileName' => $fileName,
            'folder' => $folder,
            'useUniqueFileName' => true,
        ]);
    }

    /**
     * Delete image from ImageKit
     * 
     * @param string $fileId
     * @return object
     */
    public function delete($fileId)
    {
        if (empty($fileId)) {
            return null;
        }
        return $this->imageKit->deleteFile($fileId);
    }
}
