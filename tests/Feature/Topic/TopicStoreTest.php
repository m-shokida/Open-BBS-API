<?php

namespace Tests\Feature\Topic;

use Tests\TestCase;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicStoreTest extends TestCase
{
    use RefreshDatabase;

    const API_URI = '/api/topics';

    /**
     * Undocumented function
     *
     * @param mixed $topicCategoryId
     * @param mixed $title
     * @param mixed $body
     * @param mixed $topicImage
     * @return void
     * @dataProvider storing_provider
     */
    public function test_store($topicCategoryId, $title, $body, $topicImage): void
    {
        $oldIds = Topic::all()->pluck('id');

        $this->postJson(
            self::API_URI,
            [
                'topic_category_id' => $topicCategoryId,
                'title' => $title,
                'body' => $body,
                'topic_image' => $topicImage
            ]
        )->assertCreated();

        // データが追加されているか
        $newIds = Topic::all()->pluck('id');
        $this->assertSame($oldIds->count() + 1, $newIds->count());

        // 適切なカラムへインサートされているか
        $newTopic = Topic::whereNotIn('id', $oldIds)->first();
        $this->assertSame($newTopic->topic_category_id, $topicCategoryId);
        $this->assertSame($newTopic->title, $title);
        $this->assertSame($newTopic->body, $body);
        $this->assertSame($newTopic->ip_address, '127.0.0.1');

        // 画像アップロード
        $this->assertTrue(Storage::exists(TopicController::ROOT_DIRECTORY_NAME . '/' . $newTopic->id . '/' . TopicController::TOPIC_IMAGE_NAME . '.png'));
    }

    /**
     * ストアデータプロバイダー
     *
     * @return array
     */
    public function storing_provider(): array
    {
        return [
            'store data1' => [
                1,
                'タイトル1',
                '本文1',
                UploadedFile::fake()->image('image1.png')
            ],
            'store data2' => [
                2,
                'タイトル2',
                '本文2',
                UploadedFile::fake()->image('image2.png')
            ],
            'store data3' => [
                3,
                'タイトル3',
                '本文3',
                UploadedFile::fake()->image('image3.png')
            ],
            'store data4' => [
                4,
                'タイトル4',
                '本文4',
                UploadedFile::fake()->image('image4.png')
            ]
        ];
    }

    /**
     * バリデーションエラーテスト
     * 
     * @return void
     * @dataProvider validation_error_data_provider
     */
    public function test_validate($errorTargetKeys, $topicCategoryId, $title, $body, $topicImage)
    {
        $response = $this->postJson(
            self::API_URI,
            [
                'topic_category_id' => $topicCategoryId,
                'title' => $title,
                'body' => $body,
                'topic_image' => $topicImage
            ]
        );
        $response->assertJsonValidationErrors($errorTargetKeys);
    }

    /**
     * バリデーションエラーデータプロバイダー
     *
     * @return array
     */
    public function validation_error_data_provider(): array
    {
        return [
            'The topic category id field is required.' => [
                ['topic_category_id' => 'The topic category id field is required.'],
                null,
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The topic category id must be an integer.' => [
                ['topic_category_id' => 'The topic category id must be an integer.'],
                'a',
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The selected topic category id is invalid.' => [
                ['topic_category_id' => 'The selected topic category id is invalid.'],
                13,
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The title field is required.' => [
                ['title' => 'The title field is required.'],
                1,
                null,
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The title must be a string.' => [
                ['title' => 'The title must be a string.'],
                1,
                1,
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The title must not be greater than 100 characters.' => [
                ['title' => 'The title must not be greater than 100 characters.'],
                1,
                str_repeat("*", 101),
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'The body field is required.' => [
                ['body' => 'The body field is required.'],
                1,
                "最初のトピックです",
                null,
                UploadedFile::fake()->image('test.gif')
            ],
            'The body must be a string.' => [
                ['body' => 'The body must be a string.'],
                1,
                "最初のトピックです",
                1,
                UploadedFile::fake()->image('test.gif')
            ],
            'The body must not be greater than 1000 characters.' => [
                ['body' => 'The body must not be greater than 1000 characters.'],
                1,
                "最初のトピックです",
                str_repeat("*", 1001),
                UploadedFile::fake()->image('test.gif')
            ],
            'The topic image field is required.' => [
                ['topic_image' => 'The topic image field is required.'],
                1,
                "最初のトピックです",
                '最初のトピックを投稿してみました',
                null
            ],
            'The topic image must be an image.' => [
                ['topic_image' => 'The topic image must be an image.'],
                1,
                "最初のトピックです",
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->create('test.pdf')
            ],
            'The topic image must be a file of type: png, jpg, jpeg, gif.' => [
                ['topic_image' => 'The topic image must be a file of type: png, jpg, jpeg, gif.'],
                1,
                "最初のトピックです",
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.svg')
            ],
            'The topic image must be max:2024 kilobytes.' => [
                ['topic_image' => 'The topic image must not be greater than 2024 kilobytes.'],
                1,
                "最初のトピックです",
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.jpg')->size(2025)
            ],

        ];
    }
}
