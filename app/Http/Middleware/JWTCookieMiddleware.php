<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class JWTCookieMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
       
        $cookieName = 'jwt_token';
        // $token = $request->cookie($cookieName);
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        if (! $token) {
            return response()->json(['error'=>'Token not provided'], 401);
        }
         try {
            $user = JWTAuth::setToken($token)->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['error'=>'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error'=>'Token invalid'], 401);
        } catch (\Exception $e) {
            return response()->json(['error'=>'Could not authenticate token'], 401);
        }

        $request->attributes->set('authenticated_user', $user);
        if($user){
            $request->attributes->set('authenticated_user', $user);
            
            if(config('app.auth_setter')){
                auth('api')->setUser($user);
            }
            else{
                auth()->setUser($user);
            }
        }
        
        return $next($request);
    }
}
