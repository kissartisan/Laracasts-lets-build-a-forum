<?php

namespace App;

use App\HasFavorites;
use App\RecordsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFavorites, RecordsActivity;

    protected $guarded = [];
    protected $primaryKey = 'reply_id';
    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited'];

    public static function boot()
    {
        parent::boot();

        static::created(function($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * A reply has an owner.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }


    /**
     * A reply belongs to a thread.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id', 'thread_id');
    }


    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function mentionedUsers()
    {
        preg_match_all('/\@([^\s\.]+)/', $this->body, $matches);

        return $matches[1];
    }


    /**
     * The path to a specific reply
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->reply_id}";
    }
}
