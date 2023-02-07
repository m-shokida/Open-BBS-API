<?php

namespace App\Services;

use App\Models\TopicComment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ImageUpload\CommentImageUploadService;

class TopicCommentService
{
    public function __construct(private TopicComment $topicComment)
    {
    }

    /**
     * コメントを追加する
     *
     * @param array $commentDetail
     * @param UploadedFile|null $image
     * @return void
     */
    public function createTopicComment(array $commentDetail, ?UploadedFile $image): void
    {
        DB::transaction(function () use ($commentDetail, $image) {
            $createdComment = $this->topicComment->create($commentDetail);
            if (is_null($image)) return;
            $commentImageUploadService = App::makeWith(CommentImageUploadService::class, ['topicId' => $commentDetail['topic_id'], 'topicCommentId' => $createdComment->id]);
            $createdComment->update(['image_path' => $commentImageUploadService->upload($image)]);
        });
    }

    /**
     * トピックに紐づくコメントを取得する
     *
     * @param string $topicId
     * @param integer $itemsPerPage
     * @return LengthAwarePaginator
     */
    public function getCommentsByTopic(string $topicId, int $itemsPerPage): LengthAwarePaginator
    {
        return $this->topicComment->byTopic($topicId)->oldest()->paginate($itemsPerPage);
    }
}
