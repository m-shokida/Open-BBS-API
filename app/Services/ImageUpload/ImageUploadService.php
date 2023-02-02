<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

abstract class ImageUploadService
{
    /** 画像ルートディレクトリ*/
    private const ROOT_IMAGE_DIRECTORY = 'topics';

    /** アップロード画像フォーマット */
    private const UPLOAD_IMAGE_FORMAT = 'jpg';

    protected function put(string $imagePath, string $imageName, UploadedFile $image)
    {
        return Storage::put(
            sprintf('%s/%s/%s.%s', self::ROOT_IMAGE_DIRECTORY, $imagePath, $imageName, self::UPLOAD_IMAGE_FORMAT),
            $this->convertTojpg($image)
        );
    }

    /**
     * 画像をjpgにコンバートする
     *
     * @param UploadedFile $image
     * @return string
     */
    private function convertTojpg(UploadedFile $image): string
    {
        if ($image->extension() === self::UPLOAD_IMAGE_FORMAT) return $image;
        return (string) Image::make($image)->encode(self::UPLOAD_IMAGE_FORMAT);
    }
}
