<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicComment;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicComment\StoreRequest;
use App\Services\TopicCommentService;

class TopicCommentController extends Controller
{
    /** ページ毎表示件数 */
    const ITEMS_PER_PAGE = 100;

    function __construct(private TopicCommentService $topicCommentService)
    {
    }

    /**
     * コメント一覧を取得する
     *
     * @param Topic $topic
     * @return JsonResponse
     */
    public function index(Topic $topic)
    {
        return $this->topicCommentService->getCommentsByTopic($topic->id, self::ITEMS_PER_PAGE);
    }

    /**
     * コメントを保存する
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request, Topic $topic)
    {
        $commentDetail = [
            'topic_id' => $topic->id,
            'comment' => $request->validated()['comment'],
            'ip_address' => $request->ip()
        ];

        $this->topicCommentService->createTopicComment($commentDetail, $request->file('image'));
        return response()->json(status: Response::HTTP_CREATED);
    }
}
