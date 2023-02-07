<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use App\Services\ImageUpload\ImageUploadService;

class CommentImageUploadService extends ImageUploadService
{
    /** コメント画像ディレクトリ名 */
    const COMMENT_IMAGE_DIRECTORY = 'comments';

    function __construct(private string $topicId, private string $topicCommentId)
    {
    }

    /**
     * トピックコメント画像をアップロードする
     *
     * @param UploadedFile $image
     * @return string
     */
    public function upload(UploadedFile $image): string
    {
        return parent::update(
            sprintf('%s/%s', $this->topicId, self::COMMENT_IMAGE_DIRECTORY),
            $image,
            $this->topicCommentId
        );
    }
}
