<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisConnectionTest extends TestCase
{
    public function test_can_connect_to_redis(): void
    {
        Redis::set('test_key', 'test_value');
        $value = Redis::get('test_key');

        $this->assertEquals('test_value', $value);
    }
}
