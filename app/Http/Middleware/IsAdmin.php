<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->type == 'superadministrator' || auth()->user()->type == 'admin' || auth()->user()->type == 'emp') {
           
           return $next($request);
        }
        return redirect()->to('home')->with('success', __('site.have_not_permission'));

//        return $next($request);
    }
}
