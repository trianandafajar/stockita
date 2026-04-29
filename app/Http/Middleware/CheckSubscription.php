<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! $user->hasActiveSubscription()) {
            return redirect('/subscription')->with('error', 'Silakan upgrade paket terlebih dahulu');
        }

        return $next($request);
    }
}
