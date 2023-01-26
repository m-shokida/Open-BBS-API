<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**  トピック画像ルートディレクトリ名 */
    const ROOT_IMAGE_DIRECTORY = 'topics';

    /** アップロード画像フォーマット */
    const UPLOAD_IMAGE_FORMAT = 'jpg';

    /**
     * 画像をjpgにコンバートする
     *
     * @param UploadedFile $image
     * @return string
     */
    protected function convertUpdatedImageTojpg(UploadedFile $image): string
    {
        if ($image->extension() === self::UPLOAD_IMAGE_FORMAT) return $image;
        return (string) Image::make($image)->encode(self::UPLOAD_IMAGE_FORMAT);
    }
}
