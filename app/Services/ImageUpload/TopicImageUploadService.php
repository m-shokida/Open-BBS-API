<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use App\Services\ImageUpload\ImageUploadService;

class TopicImageUploadService extends ImageUploadService
{
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    function __construct(private string $topicId)
    {
    }

    /**
     * トピック画像をアップロードする
     *
     * @param UploadedFile $image
     * @return string
     */
    public function upload(UploadedFile $image): string
    {
        return parent::update($this->topicId, $image, self::TOPIC_IMAGE_NAME);
    }
}
