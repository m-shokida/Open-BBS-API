<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Topic;
use App\Models\TopicComment;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicCommentTest extends TestCase
{
    use RefreshDatabase;

    /** 初期トピック数 */
    const TOPIC_COUNT = 3;

    /** 初期コメント数 */
    const COMMENT_COUNT = 5;

    private $topicComment;

    public function setUp(): void
    {
        parent::setUp();
        // テスト用トピック生成
        Topic::factory()->has(TopicComment::factory()->count(self::COMMENT_COUNT))->count(self::TOPIC_COUNT)->create();

        $this->topicComment = new TopicComment();
    }

    /**
     * 新コメント生成テスト
     *
     * @return void
     * @dataProvider create_new_comment_data_provider
     */
    public function test_create_new_comment(int $topicIndex, string $comment, string $ipAddress)
    {
        $topic = Topic::oldest()->get()[$topicIndex];

        $createdComment = $this->topicComment->createNewComment($topic->id, $comment, $ipAddress);

        $this->assertModelExists($createdComment);

        $this->assertDatabaseCount('topic_comments', self::COMMENT_COUNT * self::TOPIC_COUNT + 1);

        $this->assertDatabaseHas('topic_comments', [
            'id' => $createdComment->id,
            'topic_id' => $topic->id,
            'comment' => $comment,
            'ip_address' => $ipAddress,
            'plus_vote_count' => 0,
            'minus_vote_count' => 0,
            'deleted_at' => null,
        ]);
    }

    public function create_new_comment_data_provider(): array
    {
        return [
            [0, fake()->text(), fake()->ipv4()],
            [1, fake()->text(), fake()->ipv4()],
            [2, fake()->text(), fake()->ipv4()],
        ];
    }
}
