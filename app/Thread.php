<?php

namespace App;

use App\Reply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'thread_id';

	public function path()
	{
        return "/threads/{$this->channel->slug}/{$this->thread_id}";
	}

	public function replies()
	{
		return $this->hasMany(Reply::class, 'thread_id', 'thread_id');
	}

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }
}
