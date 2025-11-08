<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Determine the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request): ?string
    {
        // If the request expects JSON (e.g. API or AJAX), do not redirect
        if ($request->expectsJson()) {
            return null;
        }

        // Otherwise redirect to the login page
        return route('login');
    }
}
