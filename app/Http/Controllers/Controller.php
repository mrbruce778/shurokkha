<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

abstract class Controller
{
    function userIDFromToken($token)
    {
        $user = User::where('api_token', $token)->first();
        return $user ? $user->id : null;
    }
}
