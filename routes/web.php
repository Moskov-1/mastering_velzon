<?php

use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class,'index'])->name('backend.index');

Route::group(['prefix'=> 'admin', 'name'=>'backend.'], function () {
    Route::resource('category', CategoryController::class)->except(['show']);
    Route::resource('faq', FaqController::class)->except(['show']);
});