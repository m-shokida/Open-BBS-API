<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicCategory;
use Illuminate\Http\Response;
use App\Services\TopicService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreRequest;
use Illuminate\Http\JsonResponse;

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
     * トピックを生成する
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $topicDetail = $request->only(['topic_category_id', 'title', 'body']);
        $topicDetail['ip_address'] = $request->ip();

        $this->topicService->createTopic($topicDetail, $request->file('image'));
        return response()->json(status: Response::HTTP_CREATED);
    }

    /**
     * カテゴリ指定でトピックスを取得する
     *
     * @param TopicCategory $topicCategory
     * @return JsonResponse
     */
    public function filterByCategory(TopicCategory $topicCategory)
    {
        return $this->topicService->getTopicsByCategory($topicCategory->id, self::ITEMS_PER_PAGE);
    }
}
