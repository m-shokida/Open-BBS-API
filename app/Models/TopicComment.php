<?php

namespace App\Models;

use App\Models\Topic;
use Database\Factories\TopicCommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicComment extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = ['topic_id', 'comment', 'ip_address'];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = ['ip_address', 'updated_at', 'deleted_at'];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = ['comment_id'];

    /**
     * コメントIDを取得する
     *
     * @return void
     */
    public function getCommentIdAttribute()
    {
        return substr(hash('sha3-256', $this->attributes['ip_address']), 0, 20);
    }

    /**
     * トピックにスコープを設定
     *
     * @param [type] $query
     * @param [type] $topicId
     * @return void
     */
    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    /**
     * コメントを所有しているトピックを取得
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * モデルの新ファクトリ・インスタンスの生成
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TopicCommentFactory::new();
    }
}
