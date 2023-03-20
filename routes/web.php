<?php

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

Route::get('/', function () {
    if (\Auth::guard('admin')) {
        return redirect()->intended(route('admin_dashboard'));
    } else {
        return redirect('admin/login');
    }
})->name('/');

Route::get('/admin', function () {
    if (\Auth::guard('admin')) {
        return redirect()->intended(route('admin_dashboard'));
    } else {
        return redirect('admin/login');
    }
})->name('/');

Route::get('/home', function () {
    if (\Auth::guard('admin')) {
        return redirect()->intended(route('admin_dashboard'));
    } else {
        return redirect('admin/login');
    }
})->name('/');


Route::get('/verify/{uuid}','HomeController@verifyUser')->name('verify');
