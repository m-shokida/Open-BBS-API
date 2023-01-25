<?php

namespace Tests\Feature\Topic;

use App\Models\Topic;
use App\Models\TopicComment;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SebastianBergmann\Type\VoidType;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    const API_URI = '/api/topics';

    /** 生成するトピックスデータ数 */
    const TOPICS_COUNT = 5;

    public function setUp(): void
    {
        parent::setUp();
        Topic::factory()->has(TopicComment::factory()->count(5))->count(self::TOPICS_COUNT)->create();
    }

    /**
     * トピック取得をテスト
     *
     * @param Closure $getTopic
     * @return void
     * @dataProvider all_topics_data_provider
     */
    public function test_show_topic(Closure $getTopic)
    {
        $topic = $getTopic();

        $response = $this->getJson(
            self::API_URI . '/' . $topic['id']
        );

        $response->assertOk()->assertExactJson([
            'id' => $topic['id'],
            'topic_category_id' => $topic['topic_category_id'],
            'title' => $topic['title'],
            'body' => $topic['body'],
            'image_url' => $topic['image_url'],
            'topic_comments' => TopicComment::where('topic_id', $topic['id'])->orderBy('id')->get()->toArray()
        ])->assertJsonStructure([
            'topic_comments' => [
                '*' => [
                    'id',
                    'comment_id',
                    'comment',
                    'plus_vote_count',
                    'minus_vote_count'
                ]
            ]
        ]);
    }

    /**
     * 全トピックデータプロバイダー
     *
     * @return array
     */
    public function all_topics_data_provider(): array
    {
        $topicsData = [];
        for ($index = 0; $index < self::TOPICS_COUNT; $index++) {
            $topicsData['topic-' . ($index + 1)] =
                [
                    function () use ($index) {
                        return Topic::orderby('id')->get()->toArray()[$index];
                    }
                ];
        }

        return $topicsData;
    }

    /**
     * 全てのバリデーションをテスト
     *
     * @param array $validationErrors
     * @param Closure $getTopicId
     * @return void
     * @dataProvider validation_error_data_provider
     */
    public function test_validate(array $validationErrors, Closure $getTopicId): void
    {
        $this->getJson(
            self::API_URI . '/' . $getTopicId()
        )->assertUnprocessable()->assertJsonValidationErrors($validationErrors);
    }

    /**
     * バリデーションエラーデータプロバイダー
     *
     * @return array
     */
    public function validation_error_data_provider(): array
    {
        return [
            'topic_id : ulid' => [
                ['topic_id' => 'The topic id must be a valid ULID.'],
                function () {
                    return Str::uuid();
                }
            ],
            'topic_id : exists' => [
                ['topic_id' => 'The selected topic id is invalid.'],
                function () {
                    return Str::ulid();
                }
            ],
        ];
    }
}
