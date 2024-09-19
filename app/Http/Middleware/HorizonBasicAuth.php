<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HorizonBasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') == 'local') {
            return $next($request);
        }

        $username = config('horizon.user');
        $password = config('horizon.password');

        if (
            $request->getUser() !== $username || $request->getPassword() !== $password ||
            empty($username) || empty($password)
        ) {
            return response('Unauthorized.', 401, ['WWW-Authenticate' => 'Basic']);
        }

        return $next($request);
    }
}
