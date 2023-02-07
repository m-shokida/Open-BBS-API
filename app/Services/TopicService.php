<?php

namespace App\Services;

use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ImageUpload\TopicImageUploadService;

class TopicService
{
    public function __construct(
        private Topic $topic
    ) {
    }

    /**
     * トピックを追加する
     *
     * @param array $topicDetail
     * @param UploadedFile|null $image
     * @return void
     */
    public function createTopic(array $topicDetail, ?UploadedFile $image): void
    {
        DB::transaction(function () use ($topicDetail, $image) {
            $createdTopic = $this->topic->create($topicDetail);
            if (is_null($image)) return;
            $topicImageUploadService = App::makeWith(TopicImageUploadService::class, ['topicId' => $createdTopic->id]);
            $createdTopic->update(['image_path' => $topicImageUploadService->upload($image)]);
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

    /**
     * 週毎トピックスを取得する
     *
     * @param integer $weeksAgo
     * @param integer $itemsPerPage
     * @return LengthAwarePaginator
     */
    public function getTopicsByWeek(int $weeksAgo, int $itemsPerPage): LengthAwarePaginator
    {
        return $this->topic->weeksAgo($weeksAgo)->trend()->paginate($itemsPerPage);
    }
}
