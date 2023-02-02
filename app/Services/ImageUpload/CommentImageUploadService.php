<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use App\Services\ImageUpload\ImageUploadService;

class CommentImageUploadService extends ImageUploadService
{
    /** コメント画像ディレクトリ名 */
    const COMMENT_IMAGE_DIRECTORY = 'comments';

    /**
     * 画像をアップロードする
     *
     * @param string $topicId
     * @param string $commentId
     * @param UploadedFile $image
     * @return void
     */
    public function upload(string $topicId, string $commentId, UploadedFile $image)
    {
        $this->put(
            sprintf('%s/%s', $topicId, self::COMMENT_IMAGE_DIRECTORY),
            $commentId,
            $image
        );
    }
}
