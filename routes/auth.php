<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\Auth\AuthController;

Route::group(['as'=> 'auth.'], function () {
    Route::get('signup', [AuthController::class,'getSignUp'])->name('signup.get');
    Route::post('signup', [AuthController::class,'signup'])->name('signup.post');
    
    Route::get('login', [AuthController::class,'getLogin'])->name('login.get');
    Route::post('login', [AuthController::class,'login'])->name('login.post');

    Route::group(['middleware'=> 'admin.auth'], function () {

        Route::post('logout', [AuthController::class,'logout'])->name('logout.post');
        Route::get('reset-password', [AuthController::class,'getResetPasswordForm'])->name('reset.get');
        Route::post('reset-password', [AuthController::class,'resetPassword'])->name('reset.post');

    });

});