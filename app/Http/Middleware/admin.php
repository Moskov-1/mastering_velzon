<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) {   
        // dd($request->user());
        if(auth()->check() && auth()->user()->role == User::roles()['ADMIN']) {
            return $next($request);
        }
        $error = 'Credentials don\'t match.';
        if(User::where('email', $request->email)->first()?->role != User::roles()['ADMIN']){
            if(Auth::check()){
                // Perform full logout
                Auth::logout();

                // Invalidate the entire session
                $request->session()->invalidate();

                // Regenerate CSRF token for security
                $request->session()->regenerateToken();
            }

            $error = 'User is not an admin';
            return redirect()->route('login')->with('error',$error);
        } 
    }
}
