<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::namespace('web')->group(function (){
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', 'AuthController@showLoginPage')->name('web-view-login-page');
        Route::post('/login', 'AuthController@login')->name('web-login');
        Route::get('/register', 'AuthController@showRegisterPage')->name('web-view-register-page');
        Route::post('/register', 'AuthController@register')->name('web-register');
    });

    Route::middleware('auth:web')->group(function () {
        Route::get('/logout', 'AuthController@logout')->name('web-logout');
        Route::get('/', 'TodoController@showTodoPage')->name('web-view-todo-page');
        Route::prefix('todo')->group(function () {
            Route::post('/register', 'TodoController@register')->name('web-register-todo');
            Route::get('/toggle/{id}', 'TodoController@toggle')->name('web-toggle-todo');
            Route::get('/delete/{id}', 'TodoController@delete')->name('web-delete-todo');
        });
    });
});

