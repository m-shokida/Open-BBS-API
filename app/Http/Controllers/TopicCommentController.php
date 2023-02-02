<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicComment\StoreRequest;
use App\Services\ImageUpload\CommentImageUploadService;

class TopicCommentController extends Controller
{
    /** ページ毎表示件数 */
    const MAX_ITEM_PER_PAGE = 100;

    function __construct(private TopicComment $topicComment)
    {
    }

    /**
     * コメント一覧を取得する
     *
     * @param Topic $topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Topic $topic)
    {
        return response()->json($topic->topicComments()->oldest()->paginate(self::MAX_ITEM_PER_PAGE));
    }

    /**
     * 新コメントを保存する
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request, Topic $topic, CommentImageUploadService $commentImageUploadService)
    {
        DB::transaction(function () use ($request, $topic, $commentImageUploadService) {
            $createdComment = $this->topicComment->createNewComment($topic->id, $request->validated()['comment'], $request->ip());
            $commentImageUploadService->upload($topic->id, $createdComment->id, $request->file('image'));
        });

        return response()->json(status: Response::HTTP_CREATED);
    }
}
