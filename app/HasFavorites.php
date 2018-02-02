<?php

namespace App;

trait HasFavorites
{
    protected static function bootHasFavorites()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * A Reply can have many favorites
     * @return Illuminate\Database\Eloquent\Model
     */
    public function favorites()
    {
       return  $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * Favorite a reply
     * @param  int $userID
     * @return mixed
     */
    public function favorite($userID)
    {
        $attributes = ['user_id' => $userID ?: auth()->id()];

        if (! $this->favorites()->where($attributes)->exists())
            return $this->favorites()->create($attributes);
    }

    /**
     * Unfavorite a reply
     * @param  int $userID
     * @return mixed
     */
    public function unfavorite($userID)
    {
        $attributes = ['user_id' => $userID ?: auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * Determine if the current reply has been favorited
     * @return boolean
     */
    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * Set the favorites count (Laravel setter)
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}