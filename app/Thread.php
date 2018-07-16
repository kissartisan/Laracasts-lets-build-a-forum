<?php

namespace App;

use App\Events\ThreadHasNewReply;
use App\Notifications\ThreadWasUpdated;
use App\RecordsActivity;
use App\RecordsVisits;
use App\Reply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity, RecordsVisits;

    protected $guarded = [];
    protected $primaryKey = 'thread_id';
    protected $with = ['creator', 'channel'];
    protected $appends = ['isSubscribedTo'];

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
        $reply = $this->replies()->create($reply);

        event(new ThreadHasNewReply($reply));

        return $reply;
    }

    /**
     * Filter threads according to ThreadsFilters
     * @param  $query
     * @param  \App\Filters\ThreadFilters $filters
     * @return $this
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userID = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userID ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userID = null)
    {
        $this->subscriptions()
             ->where('user_id', $userID ?: auth()->id())
             ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class, 'thread_id', 'thread_id');
    }

    /**
     * Accessor of isSubscribedTo
     * @return [type] [description]
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                    ->where('user_id', auth()->id())
                    ->exists();
    }

    public function hasUpdatesFor($user = null)
    {
        $user = $user ?: auth()->user();
        $key = $user->visitedThreadCacheKey($this);
        // Look in the cache for the proper key.
        // Compare that carbon instance with the $thread->updated_at
        // $key = sprintf("users.%s.visits.%s",  auth()->id(), $this->thread_id);

        return  $this->updated_at > cache($key);
    }
}
