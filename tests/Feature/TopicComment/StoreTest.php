<?php

namespace Tests\Feature\TopicComment;

use Closure;
use Tests\TestCase;
use App\Models\Topic;
use Illuminate\Support\Str;
use App\Models\TopicComment;
use Illuminate\Http\Response;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\TopicCommentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ImageUpload\CommentImageUploadService;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    const API_URI_FORMAT = '/api/topics/%s/comments';

    private $topics;

    public function setUp(): void
    {
        parent::setUp();
        // テスト用トピック生成
        Topic::factory()->count(10)->create();
    }

    /**
     * データが保存されること
     *
     * @param Closure $getTopicid
     * @param string $comment
     * @param File $image
     * @return void
     * @dataProvider storing_data_provider
     */
    public function test_store(Closure $getTopicid, string $comment, File $image)
    {
        $oldCommentIds = TopicComment::all()->pluck('id');
        $topicId = $getTopicid();

        $this->postJson(
            sprintf(self::API_URI_FORMAT, $topicId),
            [
                'comment' => $comment,
                'image' => $image,
            ]
        )->assertCreated();

        // データは追加されているか
        $this->assertDatabaseCount('topic_comments', count($oldCommentIds) + 1);

        // 追加されたデータは適切か
        $newComment = TopicComment::whereNotIn('id', $oldCommentIds)->first();
        $this->assertSame($newComment->topic_id, $topicId);
        $this->assertSame($newComment->comment, $comment);
        $this->assertSame($newComment->plus_vote_count, 0);
        $this->assertSame($newComment->minus_vote_count, 0);
        $this->assertSame($newComment->ip_address, '127.0.0.1');
        $this->assertNull($newComment->deleted_at);

        // 画像が適切な場所にアップロードされているか
        $commentImageDir = 'topics/' . $newComment->topic_id . '/' . CommentImageUploadService::COMMENT_IMAGE_DIRECTORY;
        Storage::assertExists($commentImageDir . '/' . $newComment->id . '.jpg');
    }


    /**
     * ストアデータプロバイダー
     *
     * @return array
     */
    public function storing_data_provider(): array
    {
        $getMinTopicId = function () {
            return Topic::min('id');
        };

        $getMaxTopicId = function () {
            return Topic::max('id');
        };

        return [
            'min-topic&image-ping' => [
                $getMinTopicId,
                fake()->text(),
                UploadedFile::fake()->image('ping-image.png')
            ],
            'min-topic&image-jpg' => [
                $getMinTopicId,
                fake()->text(),
                UploadedFile::fake()->image('jpg-image.jpg')
            ],
            'max-topic&image-jpeg' => [
                $getMaxTopicId,
                fake()->text(),
                UploadedFile::fake()->image('jpeg-image1.jpeg')
            ],
            'max-topic&image-gif' => [
                $getMaxTopicId,
                fake()->text(),
                UploadedFile::fake()->image('gif-image1.gif')
            ],
        ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider validation_error_data_provider
     */
    public function test_validate($errorTargetKeys, Closure $getTopicid, $comment, $image)
    {
        $this->postJson(
            sprintf(self::API_URI_FORMAT, $getTopicid()),
            [
                'comment' => $comment,
                'image' => $image,
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors($errorTargetKeys);
    }

    /**
     * バリデーションエラーデータプロバイダー
     *
     * @return array
     */
    public function validation_error_data_provider(): array
    {
        $getMinTopicId = function () {
            return Topic::min('id');
        };

        return [
            'comment : required' => [
                ['comment' => 'The comment field is required.'],
                $getMinTopicId,
                null,
                UploadedFile::fake()->image('test.gif')
            ],
            'comment : string' => [
                ['comment' => 'The comment must be a string.'],
                $getMinTopicId,
                1,
                UploadedFile::fake()->image('test.gif')
            ],
            'comment : max:500' => [
                ['comment' => 'The comment must not be greater than 500 characters.'],
                $getMinTopicId,
                str_repeat("*", 501),
                UploadedFile::fake()->image('test.gif')
            ],
            'image : required' => [
                ['image' => 'The image field is required.'],
                $getMinTopicId,
                fake()->text,
                null,
            ],
            'image : image' => [
                ['image' => 'The image must be an image.'],
                $getMinTopicId,
                fake()->text,
                UploadedFile::fake()->create('test.mp3')
            ],
            'image : mimes:png,jpg,jpeg,gif' => [
                ['image' => 'The image must be a file of type: png, jpg, jpeg, gif.'],
                $getMinTopicId,
                fake()->text,
                UploadedFile::fake()->image('test.svg')
            ],
            'image : max:2024' => [
                ['image' => 'The image must not be greater than 2024 kilobytes.'],
                $getMinTopicId,
                fake()->text,
                UploadedFile::fake()->image('test.jpg')->size(2025)
            ],

        ];
    }
}
