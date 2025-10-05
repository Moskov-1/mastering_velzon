<?php

use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\ProjectController;
use App\Http\Controllers\Web\Backend\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class,'index'])->name('backend.index');

Route::group(['prefix'=> 'admin', 'as'=>'backend.'], function () {
    Route::resource('project', ProjectController::class)->except(['show']);
    Route::resource('faq', FaqController::class)->except(['show']);
});