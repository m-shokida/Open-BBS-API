<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Http\Response;
use App\Services\TopicService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreRequest;

class TopicController extends Controller
{
    const ITEMS_PER_PAGE = 50;

    /**
     * コンストラクタ
     *
     * @param Topic $topic
     */
    function __construct(private TopicService $topicService)
    {
    }

    /**
     * 新トピックを生成する
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $this->topicService->createNewTopic($request->validated(), $request->ip(), $request->file('image'));
        return response()->json(status: Response::HTTP_CREATED);
    }

    /**
     * カテゴリ別トピックスを取得する
     *
     * @param TopicCategory $topicCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterByCategory(TopicCategory $topicCategory)
    {
        return $this->topicService->getTopicsByCategory($topicCategory->id, self::ITEMS_PER_PAGE);
    }
}
