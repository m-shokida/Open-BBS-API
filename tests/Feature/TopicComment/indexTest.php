<?php

namespace Tests\Feature\TopicComment;

use App\Http\Controllers\TopicCommentController;
use App\Models\Topic;
use App\Models\TopicComment;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class indexTest extends TestCase
{
    use RefreshDatabase;

    const API_URI = '/api/topics/%s/comments';

    /** トピック数 */
    const TOPIC_COUNT = 3;

    /** コメント数 */
    const COMMENT_COUNT = 250;

    public function setUp(): void
    {
        parent::setUp();
        // テスト用トピック、コメント生成
        Topic::factory()->has(TopicComment::factory()->count(self::COMMENT_COUNT))->count(self::TOPIC_COUNT)->create();
    }

    /**
     * コメント一覧の取得をテスト
     *
     * @return void
     * @dataProvider get_comments_data_provider
     */
    public function test_get_comments(Closure $getTopic)
    {
        $topic = $getTopic();

        $response = $this->getJson(
            sprintf(self::API_URI, $topic->id)
        )->assertOk();

        $response
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    TopicCommentController::MAX_ITEM_PER_PAGE,
                    fn ($json) =>
                    $json->where('topic_id', $topic->id)->hasAll('id', 'comment_id', 'comment', 'plus_vote_count', 'minus_vote_count', 'created_at')
                        ->missing('ip_address')
                )->hasAll('current_page', 'first_page_url', 'from', 'last_page', 'last_page_url', 'links', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total')
            );

        // 指定データが含まれていること
        $exptecCommets = TopicComment::where('topic_id', $topic->id)->orderBy('id')->offset(0)->limit(TopicCommentController::MAX_ITEM_PER_PAGE)->get()->toArray();
        $response->assertJsonPath('data', $exptecCommets);
    }

    /**
     * test_get_comments用データプロバイダー
     *
     * @return array
     */
    public function get_comments_data_provider(): array
    {
        $data = [];

        for ($i = 0; $i < self::TOPIC_COUNT; $i++) {
            $data['topic-' . $i] =
                [
                    function () use ($i) {
                        return Topic::orderBy('id')->get()[$i];
                    }
                ];
        }

        return $data;
    }
}
