<?php

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * Filter threads by a given username
     * @param string $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->user_id);
    }

    /**
     * Filter the query according to the most popular threads
     * @return mixed
     */
    protected function popular()
    {
        // Explicitly clear out any ordering that has been set
        // $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter by unanswered threads
     * @return mixed
     */
    public function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}