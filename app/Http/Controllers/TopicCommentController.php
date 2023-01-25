<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicComment\StoreRequest;
use App\Models\TopicComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TopicCommentController extends Controller
{
    /**
     * 新コメントを保存する
     *
     * @param StoreRequest $request
     * @return void
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $createdComment = TopicComment::create([
            'topic_id' => $validated['topic_id'],
            'comment' => $validated['comment'],
            'ip_address' => $request->ip()
        ]);

        Storage::putFileAs(
            sprintf('topics/%s/comments', $createdComment->topic_id),
            $request->file('image'),
            $createdComment->id . '.' . $validated['image']->extension()
        );

        return response()->json(status: Response::HTTP_CREATED);
    }
}
