<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class ImageUploadService
{
    /** 画像ルートディレクトリ*/
    private const ROOT_IMAGE_DIRECTORY = 'topics';

    abstract protected function upload(UploadedFile $image);

    /**
     * 画像をアップロードする
     *
     * @param string $imagePath
     * @param string $imageName
     * @param UploadedFile $image
     * @throws UnableToWriteFile
     * @return string
     */
    protected function update(string $path, UploadedFile $image, string $name): string
    {
        return Storage::putFileAs(
            self::ROOT_IMAGE_DIRECTORY . '/' . $path,
            $image,
            $name . '.' . $image->extension()
        );
    }
}
