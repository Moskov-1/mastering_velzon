<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Web\Backend\SiteController;

Route::get('/{page?}', function ($page = null) {
    return redirect()->route('backend.dashboard.index');
})->where('page', 'home|index');

Route::get('lang/{locale}', function($locale) {
    session(['locale' => $locale]);
    return back();
})->name('lang.switch');

Route::get('/clear-cache', function(){
    return view('backend.partials.cache');
})->name('backend.settings.get.clear-cache.page');

Route::post('/clear-cache', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'All caches cleared successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Cache clear failed: ' . $e->getMessage()
        ], 500);
    }
})->name('clear.cache');


Route::get('/app-maintainace-setting', function(){
    return view('backend.layout.maintainance.toggle-mode');
})->name('backend.settings.get.maintainace.page');

require_once __DIR__ .'/auth.php';


Route::get('/success/{booking_id}', [SiteController::class, 'success'])->name('stripe.success');
Route::get('/error/{booking_id}', [SiteController::class, 'error'])->name('stripe.cancel');

Route::get('subscription/success', [SiteController::class, 'subscriptionSuccess'])->name('subscription.success');
Route::get('subscription/error', [SiteController::class, 'subscriptionError'])->name('subscription.cancel');




Broadcast::routes([
    'middleware' => ['web','auth','admin.auth'], 
]);

