<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\HasFavorites;

class Reply extends Model
{
    use HasFavorites;

    protected $guarded = [];
    protected $primaryKey = 'reply_id';
    protected $with = ['owner', 'favorites'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
