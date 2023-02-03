<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use App\Services\ImageUpload\ImageUploadService;

class TopicImageUploadService extends ImageUploadService
{
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    /**
     * トピック画像をアップロードする
     *
     * @param string $topicId
     * @param UploadedFile $image
     * @return bool
     */
    public function upload(string $topicId, UploadedFile $image): bool
    {
        return $this->put($topicId, self::TOPIC_IMAGE_NAME, $image);
    }
}
