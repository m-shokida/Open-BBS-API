<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     * 
     * @return void
     * @dataProvider storeValidationErrorDataProvider
     */
    public function test_store_validation($errorTargetKeys, $topicCategoryId, $title, $body, $topicImage)
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

    public function storeValidationErrorDataProvider(): array
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
                ['topic_image' => 'The topic image must be max:2024 kilobytes.'],
                1,
                "最初のトピックです",
                '最初のトピックを投稿してみました',
                UploadedFile::fake()->image('test.gif', 2025, 1)
            ],
            
        ];
    }
}
