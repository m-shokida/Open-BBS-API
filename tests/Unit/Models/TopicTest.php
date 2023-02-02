<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    private $topic;

    public function setUp(): void
    {
        parent::setUp();
        $this->topic = new Topic();
    }

    /**
     * 新トピック生成テスト
     *
     * @return void
     * @dataProvider createNewTopicProvider
     */
    public function testCreateNewTopic(int $topicCategoryId, string $title, string $body, string $idAddress)
    {
        $createdTopic = $this->topic->createNewTopic($topicCategoryId, $title, $body, $idAddress);

        $this->assertModelExists($createdTopic);

        $this->assertDatabaseHas('topics', [
            'id' => $createdTopic->id,
            'topic_category_id' => $topicCategoryId,
            'title' => $title,
            'body' => $body,
            'ip_address' => $idAddress,
            'deleted_at' => null
        ]);
    }

    /**
     * 新トピック生成テストデータプロバイダー
     *
     * @return array
     */
    public function createNewTopicProvider(): array
    {
        return [
            [1, fake()->title(), fake()->text(), fake()->ipv4()],
            [2, fake()->title(), fake()->text(), fake()->ipv4()],
            [3, fake()->title(), fake()->text(), fake()->ipv4()],
        ];
    }
}
