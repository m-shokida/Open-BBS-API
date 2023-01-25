<?php

namespace App\Models;

use App\Models\TopicComment;
use App\Models\TopicCategory;
use Database\Factories\TopicFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;
    use HasUlids;

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
    protected $hidden = ['ip_address', 'created_at', 'updated_at', 'deleted_at'];

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
        return $this->hasMany(TopicComment::class)->orderBy('id');
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
}
