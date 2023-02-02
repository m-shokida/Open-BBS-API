<?php

namespace Tests\Feature\Topic;

use App\Http\Controllers\TopicController;
use App\Models\Topic;
use App\Models\TopicCategory;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class FilterByCategoryTest extends TestCase
{

    use RefreshDatabase;

    const API_URI = '/api/topics/category';

    /** 生成するトピックスデータ数 */
    const TOPICS_COUNT_PER_CATEGORY = 10;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * 適切なトピックスが取得できることをテスト
     *
     * @return void
     * @dataProvider exact_topics_data_provider
     *
     */
    public function test_get_exact_topics(Closure $getCategoryId, Closure $getExactTopics)
    {
        Topic::factory()->count(TopicController::ITEMS_PER_PAGE + 1)->create([
            'topic_category_id' => TopicCategory::min('id')
        ]);
        Topic::factory()->count(TopicController::ITEMS_PER_PAGE + 1)->create([
            'topic_category_id' => TopicCategory::max('id')
        ]);

        $categoryId = $getCategoryId();

        $response = $this->getJson(
            self::API_URI . '/' . $categoryId
        )->assertOk();

        // 一ページ毎最大トピックス数
        $response->assertJsonCount(TopicController::ITEMS_PER_PAGE, 'data');

        // 対象カテゴリに所属する
        $response_data = json_decode($response->getContent(), true)['data'];
        for ($i = 0; $i < count($response_data); $i++) {
            $response->assertJsonPath('data.' . $i . '.topic_category_id', $categoryId);
        }

        // データ構造
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'topic_category_id',
                    'title',
                    'body',
                    'created_at'
                ]
            ]
        ]);

        // 適切なデータか
        $response->assertJsonPath('data', $getExactTopics());
    }

    /**
     * トピックスデータプロバイダー
     *
     * @return array
     */
    public function exact_topics_data_provider(): array
    {
        return [
            'topic1' => [
                function () {
                    return TopicCategory::min('id');
                },
                function () {
                    return Topic::where('topic_category_id', TopicCategory::min('id'))->oldest()
                        ->offset(0)->limit(TopicController::ITEMS_PER_PAGE)->get()->toArray();
                },
            ],
            'topic2' => [
                function () {
                    return TopicCategory::max('id');
                },
                function () {
                    return Topic::where('topic_category_id', TopicCategory::max('id'))->oldest()
                        ->offset(0)->limit(TopicController::ITEMS_PER_PAGE)->get()->toArray();
                },
            ],
        ];
    }


    /**
     * ルートパラメータ不正テスト
     *
     * @param Closure $getTopicId
     * @return void
     * @dataProvider invalid_root_param_data_provider
     */
    public function test_invalid_root_param_not_found(Closure $getCategoryId): void
    {
        $this->getJson(
            self::API_URI . '/' . $getCategoryId()
        )->assertNotFound();
    }


    /**
     * 不正ルートパラメータデータプロバイダー
     *
     * @return array
     */
    public function invalid_root_param_data_provider(): array
    {
        return [
            'categoryId : string' => [
                function () {
                    return fake()->text;
                }
            ],
            'categoryId : mismatched' => [
                function () {
                    return TopicCategory::max('id') + 1;
                }
            ],
        ];
    }
}
