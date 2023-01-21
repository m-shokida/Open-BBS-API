<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TopicCategory;
use Illuminate\Routing\Controller;

class TopicCategoryController extends Controller
{
    /**
     * トピックカテゴリ一覧を取得する
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(TopicCategory::orderBy('order')->get());
    }
}
