<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // try to find user by api token and route binding
        $user = User::withApiToken($request->input('api_token'))
            ->find($request->route('user'));

        // return forbidden if no user found
        if ($user === null) {
            abort(403);
        }

        // otherwise authenticate user for current request
        else {
            Auth::login($user);
        }

        return $next($request);
    }
}
