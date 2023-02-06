<?php

namespace App\Models;

use DateTimeInterface;
use App\Models\TopicComment;
use App\Models\TopicCategory;
use Illuminate\Support\Carbon;
use Database\Factories\TopicFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = ['topic_category_id', 'title', 'body', 'ip_address'];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = ['ip_address', 'updated_at', 'deleted_at'];

    /**
     * 一意の識別子を受け取るカラムの取得
     *
     * @return array
     */
    public function uniqueIds()
    {
        return ['id'];
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * トピックを所有しているカテゴリを取得
     */
    public function topicCategory()
    {
        return $this->belongsTo(TopicCategory::class);
    }

    /**
     * 所属するコメントを取得
     */
    public function topicComments()
    {
        return $this->hasMany(TopicComment::class);
    }

    /**
     * モデルの新ファクトリ・インスタンスの生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TopicFactory::new();
    }

    /**
     * カテゴリにスコープを設定
     *
     * @param Builder $query
     * @param int $categoryId
     * @return void
     */
    public function scopeCategory(Builder $query, int $categoryId)
    {
        return $query->where('topic_category_id', $categoryId);
    }

    /**
     * 週にスコープを設定
     *
     * @param Builder $query
     * @param integer $weeks
     * @return void
     */
    public function scopeWeeksAgo(Builder $query, int $weeksAgo)
    {
        $fromDate = Carbon::now()->subWeeks($weeksAgo + 1)->addDays(1);
        $toDate = Carbon::now()->subWeeks($weeksAgo);
        return $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
    }

    /**
     * コメント数ソートにスコープを設定
     *
     * @param Builder $query
     * @return void
     */
    public function scopeTrend(Builder $query)
    {
        return $query->withCount('topicComments')->orderBy('topic_comments_count', 'desc');
    }
}
