<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function() {
    Route::namespace('V1')->prefix('v1')->group(function() {
        Route::middleware('auth:api')->prefix('todos')->group(function (){
            Route::get('/', 'TodoController@getList');
            Route::post('/', 'TodoController@register');
            Route::delete('/{id}', 'TodoController@delete');
            Route::put('/{id}/toggle', 'TodoController@toggle');
        });

        Route::post('/login', 'AuthController@login');
    });
});




