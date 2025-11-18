<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLogged = auth()->check();
        if ($isLogged) {
            $user = auth()->user();
            if ($user->roles()->where('access_to','app')->count()) {
                return $next($request);
            } else {
                return $this->redirectTo($request);
            }
        } else {
            return $this->redirectTo($request);
        }
    }

    public function redirectTo($request): Response
    {
        return redirect(route('client.login'));
    }
}
