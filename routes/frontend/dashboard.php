<?php

use App\Http\Controllers\API\DashboardApiController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware'=>['api','auth:api'], 'prefix'=> 'dashboard/'], function(){
    Route::get('',[DashboardApiController::class,'profileRetrieval']);
});