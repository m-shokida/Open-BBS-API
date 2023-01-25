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
     * 複数代入不可能な属性
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = ['topic_id', 'ip_address', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * モデルの配列形態に追加するアクセサ
     *
     * @var array
     */
    protected $appends = ['comment_id'];

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCommentIdAttribute()
    {
        return substr(hash('sha3-256', $this->attributes['ip_address']), 0, 20);
    }

    /**
     * コメントを諸州しているトピックを取得
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
