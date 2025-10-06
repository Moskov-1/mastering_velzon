<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        // dd($request->user());
        if(auth()->check() && auth()->user()->role == User::roles()['ADMIN']) {
            return $next($request);
        }
        $error = 'Credentials don\'t match';
        if(auth()->user()?->role == User::roles()['ADMIN']) 
            $error = 'User is not an admin';
        return redirect()->route('auth.login.get')->with('error',$error);
    }
}
