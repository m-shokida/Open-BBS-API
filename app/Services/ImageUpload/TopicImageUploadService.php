<?php

namespace App\Services\ImageUpload;

use Illuminate\Http\UploadedFile;
use App\Services\ImageUpload\ImageUploadService;

class TopicImageUploadService extends ImageUploadService
{
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    public function upload(string $topicId, UploadedFile $image)
    {
        $this->put($topicId, self::TOPIC_IMAGE_NAME, $image);
    }
}
