<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\ShowRequest;
use App\Http\Requests\Topic\StoreRequest;
use App\Models\Topic;
use App\Models\TopicCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
{
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    const MAX_ITEM_PER_PAGE = 50;

    /**
     * 新トピックを保存
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $createdTopic = Topic::create([
                'topic_category_id' => $validated['topic_category_id'],
                'title' => $validated['title'],
                'body' => $validated['body'],
                'ip_address' => $request->ip()
            ]);

            Storage::put(
                self::ROOT_IMAGE_DIRECTORY . '/' . $createdTopic->id . '/' . self::TOPIC_IMAGE_NAME . '.' . self::UPLOAD_IMAGE_FORMAT,
                $this->convertUpdatedImageToJpg($request->file('image'))
            );
        });

        return response()->json(status: Response::HTTP_CREATED);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param TopicCategory $topicCategory
     * @return void
     */
    public function filterByCategory(Request $request, TopicCategory $topicCategory)
    {
        return response()->json(Topic::where('topic_category_id', $topicCategory->id)->orderBy('id', 'desc')->paginate(self::MAX_ITEM_PER_PAGE));
    }
}
