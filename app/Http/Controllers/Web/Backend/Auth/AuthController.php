<?php

namespace App\Http\Controllers\Web\Backend\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function getSignUp(){
        return view("backend.layout.auth.signup");
    }

    public function signup(SignUpRequest $request){
        
        $user = User::create($request->validated());

        if( Auth::attempt(['email' => $request->email,'password'=> $request->password]) ){
            return redirect()->route('backend.index')->with("success","registration completed successfully");
        }
        return back()->with(
            'error', 'Invalid credentials provided.'
        );
    }

    public function getLogin(){
        return view("backend.layout.auth.login");
    }

    public function login(Request $request){

        // dd($request->all());
        $request->validate([
            "email"=> "required|email|exists:users,email",
            "password"=> "required"
        ]);

         if( Auth::attempt(['email' => $request->email,'password'=> $request->password]) ){
            return redirect()->route('backend.index')->with("success","login completed successfully");
        }
        return back()->with(
            'error', 'Invalid credentials provided.'
        );

    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
