<?php

namespace App\Services;

use App\Models\TopicComment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ImageUpload\CommentImageUploadService;

class TopicCommentService
{
    public function __construct(private TopicComment $topicComment, private CommentImageUploadService $commentImageUploadService)
    {
    }

    /**
     * コメントを生成する
     *
     * @param array $commentDetail
     * @param UploadedFile $image
     * @return void
     */
    public function createTopicComment(array $commentDetail, UploadedFile $image): void
    {
        DB::transaction(function () use ($commentDetail, $image) {
            $createdComment = $this->topicComment->create($commentDetail);
            $this->commentImageUploadService->upload($commentDetail['topic_id'], $createdComment->id, $image);
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
