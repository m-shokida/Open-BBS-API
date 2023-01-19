<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * トピッククラス
 */
class TopicController extends Controller
{
    /**  トピック画像ルートディレクトリ名 */
    const ROOT_DIRECTORY_NAME = 'topics';
    /** トピック画像名 */
    const TOPIC_IMAGE_NAME = 'topic_image';

    /**
     * 新トピックの保存
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_category_id' => 'bail|required|integer|exists:topic_categories,id',
            'title' => 'bail|required|string|max:255',
            'body' => 'bail|required|string',
            'topic_image' => 'bail|sometimes|required|image|mimes:png,jpg,jpeg,gif|max:2048'
        ]);

        try {

            DB::transaction(function () use ($request, $validated) {

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
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([], Response::HTTP_CREATED);
    }
}
