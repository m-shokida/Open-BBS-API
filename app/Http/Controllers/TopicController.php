<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\ShowRequest;
use App\Http\Requests\Topic\StoreRequest;
use App\Models\Topic;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
{
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

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
                $this->convertUpdatedImageToJpg($request->file('topic_image')),
            );
        });

        return response()->json(status: Response::HTTP_CREATED);
    }

    /**
     * トピックとそれに紐づくコメントを取得
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowRequest $request)
    {
        return response()->json(Topic::with('topicComments')->find($request->validated()['topic_id']));
    }
}
