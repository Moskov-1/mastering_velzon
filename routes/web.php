<?php

use App\Http\Controllers\Web\Backend\Auth\AuthController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\ProjectController;
use App\Http\Controllers\Web\Backend\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class,'index'])->name('backend.index');

Route::group(['as'=> 'auth.'], function () {
    Route::get('signup', [AuthController::class,'getSignUp'])->name('signup.get');
    Route::post('signup', [AuthController::class,'signup'])->name('signup.post');
});

Route::group(['prefix'=> 'admin', 'as'=>'backend.'], function () {
    Route::resource('project', ProjectController::class)->except(['show']);
    Route::resource('faq', FaqController::class)->except(['show']);
});