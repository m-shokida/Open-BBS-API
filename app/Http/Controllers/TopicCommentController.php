<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicComment\StoreRequest;
use App\Models\TopicComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TopicCommentController extends Controller
{
    /** コメント画像ディレクトリ名 */
    const COMMENT_IMAGE_DIRECTORY = 'comment';

    /**
     * 新コメントを保存する
     *
     * @param StoreRequest $request
     * @return void
     */
    public function store(StoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $createdComment = TopicComment::create([
                'topic_id' => $validated['topic_id'],
                'comment' => $validated['comment'],
                'ip_address' => $request->ip()
            ]);

            Storage::put(
                sprintf('%s/%s/%s/%s', self::ROOT_IMAGE_DIRECTORY, $createdComment->topic_id, self::COMMENT_IMAGE_DIRECTORY, $createdComment->id . '.' . self::UPLOAD_IMAGE_FORMAT),
                $this->convertUpdatedImageToJpg($request->file('image')),
            );
        });

        return response()->json(status: Response::HTTP_CREATED);
    }
}
