<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    public function subject()
    {
        return $this->morphTo();
    }

    public static function feed($user, $limit = 50)
    {
        return static::where('user_id', $user->user_id)
                ->with('subject')
                ->latest()
                ->take($limit)
                ->get()
                ->groupBy(function($activity) {
                    return $activity->created_at->format('Y-m-d');
                });
    }
}
