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
    Route::namespace('V1')->prefix('v1')->middleware('auth:api')->group(function() {
        Route::prefix('todos')->group(function (){
            Route::get('/', 'TodoController@getList')->name('api-todo-get');
            Route::post('/', 'TodoController@register')->name('api-todo-register');
            Route::delete('/{id}', 'TodoController@delete')->name('api-todo-delete');
            Route::put('/{id}/toggle', 'TodoController@toggle')->name('api-todo-toggle');
        });
    });
});




