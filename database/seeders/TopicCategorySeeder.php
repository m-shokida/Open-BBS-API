<?php

namespace Database\Seeders;

use App\Models\TopicCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TopicCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TopicCategory::insert([
            ['name' => '生活'],
            ['name' => '観光・レジャー'],
            ['name' => 'グルメ'],
            ['name' => '留学'],
            ['name' => '仕事'],
            ['name' => '住居'],
            ['name' => 'ビザ'],
            ['name' => '医療'],
            ['name' => '準備'],
            ['name' => '交通・飛行機'],
            ['name' => '育児・子育て'],
            ['name' => 'その他']
        ]);
    }
}
