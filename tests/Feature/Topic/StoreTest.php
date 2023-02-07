<?php

namespace Tests\Feature\Topic;

use Tests\TestCase;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ImageUpload\TopicImageUploadService;

class StoreTest extends TestCase
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
     * @dataProvider storing_data_provider
     */
    public function test_store($topicCategoryId, $title, $body, $topicImage): void
    {
        $oldIds = Topic::all()->pluck('id');

        $params = [
            'topic_category_id' => $topicCategoryId,
            'title' => $title,
            'body' => $body
        ];

        if (isset($topicImage)) {
            $params['image'] = $topicImage;
        }

        $this->postJson(
            self::API_URI,
            $params
        )->assertCreated();

        // データが追加されているか
        $newIds = Topic::all()->pluck('id');
        $this->assertSame($oldIds->count() + 1, $newIds->count());

        // 追加されたデータは適切か
        $newTopic = Topic::whereNotIn('id', $oldIds)->first();
        $this->assertSame($newTopic->topic_category_id, $topicCategoryId);
        $this->assertSame($newTopic->title, $title);
        $this->assertSame($newTopic->body, $body);
        $this->assertSame($newTopic->ip_address, '127.0.0.1');

        // 画像が適切な場所にアップロードされているか
        Storage::assertExists($newTopic->image_path);
    }

    /**
     * ストアデータプロバイダー
     *
     * @return array
     */
    public function storing_data_provider(): array
    {
        return [
            'store data1' => [
                1,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('image1.png')
            ],
            'store data2' => [
                2,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('image2.jpg')
            ],
            'store data3' => [
                3,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('image3.jpeg')
            ],
            'store data4' => [
                4,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('image4.gif')
            ],
            'store data5' => [
                5,
                fake()->title(),
                fake()->text(),
                null
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
        $this->postJson(
            self::API_URI,
            [
                'topic_category_id' => $topicCategoryId,
                'title' => $title,
                'body' => $body,
                'image' => $topicImage
            ]
        )->assertJsonValidationErrors($errorTargetKeys);
    }

    /**
     * バリデーションエラーデータプロバイダー
     *
     * @return array
     */
    public function validation_error_data_provider(): array
    {
        return [
            'topic_category_id : required' => [
                ['topic_category_id' => 'The topic category id field is required.'],
                null,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'topic_category_id : integer' => [
                ['topic_category_id' => 'The topic category id must be an integer.'],
                'a',
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'topic_category_id : exists' => [
                ['topic_category_id' => 'The selected topic category id is invalid.'],
                13,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'title : required' => [
                ['title' => 'The title field is required.'],
                1,
                null,
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'title : string' => [
                ['title' => 'The title must be a string.'],
                1,
                1,
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'title : max:100' => [
                ['title' => 'The title must not be greater than 100 characters.'],
                1,
                str_repeat("*", 101),
                fake()->text(),
                UploadedFile::fake()->image('test.gif')
            ],
            'body : required' => [
                ['body' => 'The body field is required.'],
                1,
                fake()->title(),
                null,
                UploadedFile::fake()->image('test.gif')
            ],
            'body : string' => [
                ['body' => 'The body must be a string.'],
                1,
                fake()->title(),
                1,
                UploadedFile::fake()->image('test.gif')
            ],
            'body : max:1000' => [
                ['body' => 'The body must not be greater than 1000 characters.'],
                1,
                fake()->title(),
                str_repeat("*", 1001),
                UploadedFile::fake()->image('test.gif')
            ],
            'image : required' => [
                ['image' => 'The image field is required.'],
                1,
                fake()->title(),
                fake()->text(),
                null
            ],
            'image : image' => [
                ['image' => 'The image must be an image.'],
                1,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->create('test.pdf')
            ],
            'image : mimes:png,jpg,jpeg,gif' => [
                ['image' => 'The image must be a file of type: png, jpg, jpeg, gif.'],
                1,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('test.svg')
            ],
            'image : max:2024' => [
                ['image' => 'The image must not be greater than 2024 kilobytes.'],
                1,
                fake()->title(),
                fake()->text(),
                UploadedFile::fake()->image('test.jpg')->size(2025)
            ],

        ];
    }
}
