<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    /**
     * Show the user's profile
     * @param  User   $user [description]
     * @return \Response
     */
    public function show(User $user)
    {
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => $this->getActivity($user)
        ]);
    }

    /**
     * Get the user's activity
     * @param  User   $user
     * @return mixed
     */
    public function getActivity(User $user)
    {
        return $user->activity()->with('subject')->latest()->take(50)->get()->groupBy(function($activity) {
            return $activity->created_at->format('Y-m-d');
        });
    }

}
