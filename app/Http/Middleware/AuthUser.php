<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class AuthUser
{

    public static $user;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get token from header request.
        $tokenHeader = $request->header('AuthorizationToken');

        //If user exist (by token), set the user.
        if (User::where('token', $tokenHeader)->first()) {
            self::$user = User::where('token', $tokenHeader)->first();

            return $next($request);
        } else {
            //If toke not valid, return error message.
            return response()->json('Bad Request', 400);
        }
    }

    /***
     * Return User if token valid.
     *
     * @return mixed
     */
    public static function getUser()
    {
        return self::$user;
    }
}
