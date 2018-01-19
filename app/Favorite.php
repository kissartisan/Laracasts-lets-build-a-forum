<?php

namespace App;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use RecordsActivity;

    protected $guarded = [];
    protected $primaryKey = 'favorite_id';

    public function favorited()
    {
        return $this->morphTo();
    }
}
