<?php

namespace App\Http\Controllers\Web\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function getSignUp(){
        return view("backend.layout.auth.signup");
    }

    public function signup(Request $request){
        
    }

    public function getLogin(){
        // return view("");
    }
}
