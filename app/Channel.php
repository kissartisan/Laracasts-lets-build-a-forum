<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $primaryKey = 'channel_id';

    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function threads()
    {
        return $this->hasMany(Thread::class, 'channel_id', 'channel_id');
    }
}
