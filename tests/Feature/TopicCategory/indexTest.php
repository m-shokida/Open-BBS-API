<?php

namespace Tests\Feature\TopicCategory;

use Tests\TestCase;
use App\Models\TopicCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class indexTest extends TestCase
{
    const API_URI = '/api/topic-categories';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * トピックカテゴリ取得テスト
     *
     * @return void
     */
    public function test_get_topic_categories()
    {
        $response = $this->getJson(self::API_URI);

        // ステータスコードは適切か
        $response->assertOk();

        //データは適切か
        $topicCategories = TopicCategory::oldest('id')->get()->toArray();
        $response->assertExactJson($topicCategories);
    }
}
