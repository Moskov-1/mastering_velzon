<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $cookieName = 'jwt_token';
        // $token = $request->cookie($cookieName);
        
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        if (! $token) {
            Log::debug('inside not autthorized ', ['cookie name'=> $token, 'request', $request]);
            return $next($request);
        }
        try {
            $user = JWTAuth::setToken($token)->authenticate();
            // auth()->loginUsingId($user->id);
        } catch (TokenExpiredException $e) {
            return $next($request);
            // return response()->json(['error'=>'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return $next($request);
            // return response()->json(['error'=>'Token invalid'], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'error'=>'Could not authenticate token'], 401);
        }
        
        if($user){
            $request->attributes->set('authenticated_user', $user);
            auth('api')->setUser($user);
        }

        return $next($request);
    }
}
