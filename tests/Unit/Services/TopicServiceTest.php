<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Topic;
use App\Models\TopicComment;
use App\Services\TopicService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ImageUpload\TopicImageUploadService;

class TopicServiceTest extends TestCase
{
    use RefreshDatabase;

    private $topicService;

    public function setUp(): void
    {
        parent::setUp();

        for ($i = 0; $i < 50; $i++) {
            Topic::factory()->state([
                'created_at' => Carbon::now()->subDays($i),
            ])->has(TopicComment::factory()->count(mt_rand(1, 10)))->count(1)->create();
        }

        $this->topicService = new TopicService(new Topic());
    }

    /**
     * 週毎トピックス取得テスト
     *
     * @return void
     * @dataProvider topicsByWeekDataProvider
     */
    public function testGetTopicsByWeek(int $weeksAgo, int $expectedCount)
    {
        $topicsByWeek = $this->topicService->getTopicsByWeek($weeksAgo, 50)->toArray();
        $topics = $topicsByWeek['data'];

        // 取得数
        $this->assertCount($expectedCount, $topics);

        // 日付範囲
        $fromDate = Carbon::now()->subWeeks($weeksAgo + 1)->addDays(1)->setTime(0, 0, 0);
        $toDate = Carbon::now()->subWeeks($weeksAgo)->setTime(0, 0, 0);
        foreach ($topics as $topic) {
            $this->assertTrue(Carbon::parse($topic['created_at'])->setTime(0, 0, 0)->between($fromDate, $toDate));
        }

        // コメント数ソート
        array_multisort(array_column($topics, 'topic_comments_count'), SORT_DESC, $topics);
        $this->assertSame($topics, $topicsByWeek['data']);
    }

    /**
     * 週毎トピックス取得用データプロバイダー
     *
     * @return array
     */
    public function topicsByWeekDataProvider(): array
    {
        return [
            [0, 7],
            [1, 7],
            [2, 7],
            [3, 7],
            [4, 7],
            [5, 7],
            [6, 7],
            [7, 1]
        ];
    }
}
