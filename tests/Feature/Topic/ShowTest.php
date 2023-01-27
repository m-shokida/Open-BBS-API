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
        Topic::factory()->count(self::TOPICS_COUNT)->create();
    }

    /**
     * トピック取得をテスト
     *
     * @param Closure $getTopic
     * @return void
     * @dataProvider get_topic_detail_data_provider
     */
    public function test_get_topic_detail(Closure $getTopic)
    {
        $topic = $getTopic();

        $response = $this->getJson(
            self::API_URI . '/' . $topic['id']
        )->assertOk();

        $response->assertExactJson([
            'id' => $topic['id'],
            'topic_category_id' => $topic['topic_category_id'],
            'title' => $topic['title'],
            'body' => $topic['body'],
            'image_url' => $topic['image_url'],
            'created_at' => $topic['created_at']
        ]);
    }

    /**
     * トピックデータプロバイダー
     *
     * @return array
     */
    public function get_topic_detail_data_provider(): array
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
}
