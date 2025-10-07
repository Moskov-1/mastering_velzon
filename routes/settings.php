<?php

use App\Http\Controllers\Web\Backend\Settings\MailController;
use PHPUnit\Event\Telemetry\System;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\Settings\SystemController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;

Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
    Route::controller(ProfileController::class)->name('profile.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('upload-avatar','avatar')->name('avatar.upload');
        Route::post('upload-banner','banner')->name('banner.upload');
    });

    Route::controller(SystemController::class)->prefix('system/')->name('system.')->group(function(){
        Route::get('', 'index')->name('index');
        Route::put('update', 'update')->name('update');
    });

    Route::controller(MailController::class)->prefix('mail/')->name('mail.')->group(function(){
        Route::get('', 'index')->name('index');
        Route::put('update', 'update')->name('update');
    });
});