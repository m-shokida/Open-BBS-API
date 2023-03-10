<?php

use App\Http\Controllers\TopicCategoryController;
use App\Http\Controllers\TopicCommentController;
use App\Http\Controllers\TopicController;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('topics')->controller(TopicController::class)->group(function () {
    Route::post('/', 'store')->middleware('file.valid');
    Route::get('/{topic}', function (Topic $topic) {
        return $topic;
    });
    Route::get('/category/{topicCategory}', 'filterByCategory');
    Route::get('/week/{weeksAgo}', 'getTopicsByWeek');
    Route::prefix('{topic}/comments')->controller(TopicCommentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->middleware('file.valid');
    });
});

Route::prefix('topic-categories')->controller(TopicCategoryController::class)->group(function () {
    Route::get('/', 'index');
});
