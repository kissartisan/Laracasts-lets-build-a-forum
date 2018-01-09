<?php

namespace App;

use App\HasFavorites;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFavorites, RecordsActivity;

    protected $guarded = [];
    protected $primaryKey = 'reply_id';
    protected $with = ['owner', 'favorites'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
