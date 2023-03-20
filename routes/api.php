<?php

use Illuminate\Http\Request;

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

/** Login */
Route::post('/login', 'API\LoginController@login');

/** Login */
Route::post('/send-otp', 'API\LoginController@sendOtp');

/** Logout */
Route::post('/logout', 'API\LoginController@logout');

/** Register */
Route::post('/register', 'API\RegisterController@register');

/** Forget Password */
Route::post('/forget_password', 'API\ResetPasswordController@forgotPassword');


/** Category List */
Route::post('/category-list','API\MasterController@categoryList');

/** Banner List */
Route::post('/banner-list','API\MasterController@bannerList');

/** Product List */
Route::post('/product-list','API\MasterController@productList');

/** Product Detail */
Route::post('/product-detail','API\MasterController@productDetail');

/** Auth API Group */
Route::group(['middleware' => ['auth:api']], function() {

    Route::group(['middleware' => ['activation']], function() {

        /** Job Details*/
        Route::post('/job_details', 'API\JobController@detail');

        /** All Job List*/
        Route::post('/all_jobs', 'API\JobController@allJobs');

        /** Contact Us */
        Route::post('/contact_us', 'API\UserController@contactUs');

        /** Support */
        Route::post('/support', 'API\UserController@support');

        /** Profile Detail */
        Route::post('/profile_detail', 'API\UserController@profileDetail');

        /** Update Profile */
        Route::post('/update_profile', 'API\UserController@updateProfile');

        /** CMS Pages */
        Route::post('/cms', 'API\CMSController@detail');


        /** Notifications List */
        Route::post('/notification_list','API\MasterController@notificationList');

        /** Change Password */
        Route::post('/change_password', 'API\UserController@changePassword');

        /** Refresh Token */
        Route::post('/refresh_token', 'API\UserController@refreshToken');
    
    });
});