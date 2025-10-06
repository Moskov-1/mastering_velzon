<?php

namespace App\Http\Controllers\Web\Backend\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;

class AuthController extends Controller
{
    public function getSignUp(){
        return view("backend.layout.auth.signup");
    }

    public function signup(SignUpRequest $request){
        
        $user = User::create($request->validated());
        dd($user);
        
    }

    public function getLogin(){
        // return view("");
    }
}
