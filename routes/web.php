<?php

use App\Http\Controllers\Web\Backend\Auth\AuthController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\ProjectController;
use App\Http\Controllers\Web\Backend\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/{page?}', function ($page = null) {
    return redirect()->route('backend.index');
})->where('page', 'home|index');


require_once __DIR__ .'/auth.php';

Route::group(['prefix'=> 'admin/', 'as'=>'backend.', 'middleware'=> ['admin.auth']], function () {
    Route::get('/', [SiteController::class,'index'])->name('index');
    Route::resource('project', ProjectController::class)->except(['show']);

    Route::post('faq/status/{id}', [FaqController::class,'status'])->name('faq.status');
    Route::resource('faq', FaqController::class)->except(['show']);

    
    require_once __DIR__ .'/settings.php';
});

