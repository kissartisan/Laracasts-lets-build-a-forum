<?php

namespace App;

use App\RecordsActivity;
use App\Reply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];
    protected $primaryKey = 'thread_id';
    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
            /**
             * OR
             */
            // $thread->replies->each(function($reply) {
            //     $reply->delete();
            // });
        });
    }

	public function path()
	{
        return "/threads/{$this->channel->slug}/{$this->thread_id}";
	}

    /**
     * A thread have many replies
     * @return \Illuminate\Database\Eloquent\Relations\
     */
	public function replies()
	{
		return $this->hasMany(Reply::class, 'thread_id', 'thread_id');
	}

    /**
     * A thread belongs to a creator
     * @return \Illuminate\Database\Eloquent\Relations\
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * A thread belongs to a channel
     * @return \Illuminate\Database\Eloquent\Relations\
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id', 'channel_id');
    }

    /**
     * Add a reply to a thread
     * @param array $reply
     * @return Reply
     */
    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }

    /**
     * Filter threads according to ThreadsFilters
     * @param  $query
     * @param  \App\Filters\ThreadFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
