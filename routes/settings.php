<?php

use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
    Route::controller(ProfileController::class)->name('profile.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('upload-avatar','avatar')->name('avatar.upload');
        Route::post('upload-banner','banner')->name('banner.upload');
    });

    
});