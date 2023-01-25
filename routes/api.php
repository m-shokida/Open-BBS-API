<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TopicCommentController;
use App\Http\Controllers\TopicCategoryController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('topics')->controller(TopicController::class)->group(function () {
    Route::post('/', 'store')->middleware('file.valid');
    Route::prefix('{topic_id}/comments')->controller(TopicCommentController::class)->group(function () {
        Route::post('/', 'store')->middleware('file.valid');
    });
});

Route::prefix('topic-categories')->controller(TopicCategoryController::class)->group(function () {
    Route::get('/', 'index');
});
