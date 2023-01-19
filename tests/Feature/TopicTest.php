<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * 
     * @return void
     * @dataProvider validationErrorDataProvider
     */
    public function test_validation($errorTargetKeys, $topicCategoryId, $title, $body, $topicImage)
    {
        $response = $this->postJson(
            '/api/topics',
            [
                'topic_category_id' => $topicCategoryId,
                'title' => $title,
                'body' => $body,
                'topic_image' => $topicImage
            ]
        );
        $response->assertJsonValidationErrors($errorTargetKeys);
    }

    public function validationErrorDataProvider(): array
    {
        return [
            'topic_category_id is null' => [
                ['topic_category_id' => 'required'],
                null,
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'topic_category_id is not integer' => [
                ['topic_category_id' => 'integer'],
                'a',
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'topic_category_id dose not exists' => [
                ['topic_category_id' => 'invalid'],
                13,
                '最初のトピックです',
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'title is null' => [
                ['title' => 'required'],
                1,
                null,
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'title is not string' => [
                ['title' => 'string'],
                1,
                1,
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
            'title must not be greater than 255 characters' => [
                ['title' => 'greater'],
                1,
                str_repeat("*", 256),
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif')
            ],
        ];
    }
}
