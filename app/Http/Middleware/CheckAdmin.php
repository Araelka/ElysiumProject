<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()){
            return $next($request);
        }
        
        return redirect()->back()->with('error', "У вас нет прав администратора.");
    }
}
