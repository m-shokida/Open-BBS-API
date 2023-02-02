<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Models\TopicCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreRequest;
use App\Services\ImageUpload\TopicImageUploadService;

class TopicController extends Controller
{
    const MAX_ITEM_PER_PAGE = 50;

    /**
     * コンストラクタ
     *
     * @param Topic $topic
     */
    function __construct(private Topic $topic)
    {
    }

    /**
     * 新トピックを保存
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, TopicImageUploadService $topicImageUploadService)
    {
        DB::transaction(function () use ($request, $topicImageUploadService) {
            $validated = $request->validated();

            $createdTopic = $this->topic->createNewTopic(
                $validated['topic_category_id'],
                $validated['title'],
                $validated['body'],
                $request->ip()
            );

            $topicImageUploadService->upload($createdTopic->id, $request->file('image'));
        });

        return response()->json(status: Response::HTTP_CREATED);
    }

    /**
     * type here something
     *
     * @param TopicCategory $topicCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterByCategory(TopicCategory $topicCategory)
    {
        return $this->topic->where('topic_category_id', $topicCategory->id)->oldest()->paginate(self::MAX_ITEM_PER_PAGE);
    }
}
