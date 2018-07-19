<?php

namespace App;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path'
    ];
    protected $primaryKey = 'user_id';
    protected $casts = [
        'confirmed' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class, 'user_id', 'user_id')->latest();
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class, 'user_id', 'user_id')->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class, 'user_id', 'user_id');
    }

    public function read($thread)
    {
        // Simulate that the user visited the thread
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->save();
    }

    public function getAvatarPathAttribute($avatar)
    {
        $avatarPath = $avatar ?: 'images/avatars/default.png';
        return  '/storage/' . $avatarPath;
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf("users.%s.visits.%s",  $this->user_id , $thread->thread_id);
    }
}
