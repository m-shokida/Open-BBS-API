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
     * トピックを生成する
     *
     * @param array $topicDetail
     * @param UploadedFile $image
     * @return void
     */
    public function createTopic(array $topicDetail, UploadedFile $image): void
    {
        DB::transaction(function () use ($topicDetail, $image) {
            $createdTopic = $this->topic->create($topicDetail);
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
