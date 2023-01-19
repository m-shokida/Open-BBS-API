<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class ExampleTest extends TestCase
{
    public function setUp(): void
    {
        // 祖先クラスの setUp() を忘れずにコールする。
        parent::setUp();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {
        // Log::info(get_meta_tags('https://news.yahoo.co.jp/articles/6154cf9cf2c895dfef4e8a5ec6cf08962d1f4c6a'));
        // Log::info(get_meta_tags('https://girlschannel.net/topics/4402168/'));

        
        $this->assertTrue(true);
    }
}
