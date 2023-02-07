<?php

namespace Tests\Feature\Topic;

use Tests\TestCase;
use App\Models\Topic;
use App\Models\TopicComment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTopicByWeekTest extends TestCase
{
    use RefreshDatabase;

    const API_URI = '/api/topics/week';

    public function setUp(): void
    {
        parent::setUp();

        for ($i = 0; $i < 50; $i++) {
            Topic::factory()->state([
                'created_at' => Carbon::now()->subDays($i),
            ])->has(TopicComment::factory()->count(mt_rand(1, 10)))->count(1)->create();
        }
    }

    /**
     * 週毎トピックス取得テスト
     *
     * @return void
     * @dataProvider topicsByWeekDataProvider
     */
    public function testGetTopicsByWeek(int $weeksAgo, int $expectedCount)
    {
        $response = $this->getJson(self::API_URI . '/' . $weeksAgo);

        // ステータスコード
        $response->assertOk();

        // JSON構造
        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->hasAll('current_page', 'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total')
                    ->has(
                        'data',
                        $expectedCount,
                        fn (AssertableJson $topics) =>
                        $topics->hasAll('id', 'topic_category_id', 'title', 'body', 'image_path', 'topic_comments_count', 'created_at')
                    )
            );
    }

    /**
     * 週毎トピックス取得用データプロバイダー
     *
     * @return array
     */
    public static function topicsByWeekDataProvider(): array
    {
        return [
            [0, 7],
            [1, 7],
            [2, 7],
            [3, 7],
            [4, 7],
            [5, 7],
            [6, 7],
            //    [7, 1]
        ];
    }
}
