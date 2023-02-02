<?php

namespace App\Services;

use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ImageUpload\TopicImageUploadService;

class TopicService
{
    public function __construct(private Topic $topic, private TopicImageUploadService $topicImageUploadService)
    {
    }

    /**
     * 新トピックを生成する
     *
     * @param array $validated
     * @param string $ipAddress
     * @param UploadedFile $image
     * @return void
     */
    public function createNewTopic(array $validated, string $ipAddress, UploadedFile $image)
    {
        DB::transaction(function () use ($validated,  $ipAddress, $image) {
            $createdTopic = $this->topic->createNewTopic(
                $validated['topic_category_id'],
                $validated['title'],
                $validated['body'],
                $ipAddress
            );

            $this->topicImageUploadService->upload($createdTopic->id, $image);
        });
    }

    /**
     * カテゴリ毎トピックスを取得する
     *
     * @param integer $categoryId
     * @param integer $itemsPerPage
     * @return LengthAwarePaginator
     */
    public function getTopicsByCategory(int $categoryId, int $itemsPerPage): LengthAwarePaginator
    {
        return $this->topic->category($categoryId)->oldest()->paginate($itemsPerPage);
    }
}
