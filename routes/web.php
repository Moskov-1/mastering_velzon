<?php

use App\Http\Controllers\Web\Backend\Auth\AuthController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\ProjectController;
use App\Http\Controllers\Web\Backend\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/{page?}', function ($page = null) {
    return redirect()->route('backend.index');
})->where('page', 'home|index');


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

Route::group(['prefix'=> 'admin', 'as'=>'backend.', 'middleware'=> ['admin.auth']], function () {
    Route::get('/', [SiteController::class,'index'])->name('index');
    Route::resource('project', ProjectController::class)->except(['show']);
    Route::resource('faq', FaqController::class)->except(['show']);
});