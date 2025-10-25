<?php


use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\SystemUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\SiteController;
use App\Http\Controllers\Web\Backend\ProjectController;


use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

Route::group([ 'as'=>'backend.'], function () {

    require_once __DIR__.'/queue.php';

    Route::get('/', [SiteController::class,'index'])->name('dashboard.index');
    Route::resource('project', ProjectController::class)->except(['show']);

    Route::group(['as'=>'feature.'], function(){
        Route::post('faq/status/{id}', [FaqController::class,'status'])->name('faq.status');
        Route::resource('faq', FaqController::class)->except(['show']);
    });

    Route::group(['as'=>'category.'], function(){
        Route::post('category/status/{id}', [CategoryController::class,'status'])->name('category.status');
        Route::resource('category', CategoryController::class)->except(['show']);
    });

    Route::post('page/status/{id}', [PageController::class,'status'])->name('page.status');
    Route::resource('page', PageController::class)->except(['show']);
    
    Route::post('system-user/status/{id}', [SystemUserController::class,'status'])
    ->name('system-user.status');
    
    Route::resource('system-user', SystemUserController::class)
    ->except(['show']);
    

    require_once __DIR__ .'/settings.php';
});
