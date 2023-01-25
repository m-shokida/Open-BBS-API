<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Topic;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTopicRequest;

class TopicController extends Controller
{
    /**  トピック画像ルートディレクトリ名 */
    const ROOT_DIRECTORY_NAME = 'topics';
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    /**
     * 新トピックを投稿する
     *
     * @param  \Illuminate\Http\StoreTopicRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTopicRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $createdTopic = Topic::create([
                'topic_category_id' => $validated['topic_category_id'],
                'title' => $validated['title'],
                'body' => $validated['body'],
                'ip_address' => $request->ip()
            ]);

            Storage::putFileAs(
                self::ROOT_DIRECTORY_NAME . '/' . $createdTopic->id,
                $request->file('topic_image'),
                self::TOPIC_IMAGE_NAME . '.' . $request->topic_image->extension()
            );
        });

        return response()->json(status: Response::HTTP_CREATED);
    }
}
