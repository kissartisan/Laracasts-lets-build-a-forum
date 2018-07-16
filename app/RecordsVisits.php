<?php

namespace App;

use Illuminate\Support\Facades\Redis;

trait RecordsVisits
{
    protected function visitsCacheKey()
    {
        return "threads.{$this->thread_id}.visits";
    }

    public function recordVisit()
    {
        $test = Redis::incr($this->visitsCacheKey());

        return $this;
    }

    public function visits()
    {
        return Redis::get($this->visitsCacheKey()) ?? 0;
    }

    public function resetVisits()
    {
        Redis::del($this->visitsCacheKey());

        return $this;
    }
}