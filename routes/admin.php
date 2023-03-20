<?php

/* Admin login routes */

Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('login', 'Auth\AdminLoginController@login')->name('admin.login.post');
Route::post('logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
Route::get('password/reset', 'Auth\Admin\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\Admin\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\Admin\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\Admin\ResetPasswordController@reset')->name('admin/password/reset');


Route::group(['middleware' => 'admin', 'namespace' => 'Admin'], function () {
    /** Dashboard route */
    Route::get('/dashboard', 'DashboardController@index')->name('admin_dashboard');
    /**User management route list */
    Route::group(['prefix' => 'user'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {
            Route::get('/', 'UserController@index')->name('user_index');
            Route::get('create', 'UserController@create')->name('user_create');
            Route::get('edit/{uuid}', 'UserController@create')->name('user_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'user/store', [
                'as' => 'user_store',
                'uses' => 'UserController@store',
            ]);
            Route::delete('delete', 'UserController@destroyUser')->name('user_delete');
            Route::get('user_datatable', 'UserController@getdata')->name('user_datatable');
            Route::post('active_inactive', 'UserController@updateStatus')->name('user_active_inactive');
            Route::post('user_multi_delete', 'UserController@multideleteUser')->name('user_multi_delete');
            Route::get('user_detail/{id}', 'UserController@detail')->name('user_detail');
            Route::get('user_verify/{id}/{status}', 'UserController@verify')->name('user_verify');
            Route::post('jobDetail','UserController@jobDetail')->name('user.job');
            Route::post('multiUserJobDetail','UserController@multiUserJobDetail')->name('user.job_multiple');
            Route::post('changePosition', 'UserController@changePosition')->name('user.change-position');
            Route::post('changeRole', 'UserController@changeRole')->name('user.change-role');
        });
    });

    /**Role management */
    Route::group(['prefix' => 'role'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {
            Route::get('/', 'RoleController@index')->middleware([])->name('role_index');
            Route::get('create', 'RoleController@create')->middleware([])->name('role_create');
            Route::get('edit/{id}', 'RoleController@create')->middleware([])->name('role_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'role/store', [
                'as' => 'role_store',
                'uses' => 'RoleController@store',
            ]);
            Route::delete('delete', 'RoleController@destroyRole')->middleware([])->name('role_delete');
            Route::get('role_datatable', 'RoleController@getdata')->middleware([])->name('role_datatable');
            Route::post('role_multi_delete', 'RoleController@multideleteRole')->middleware([])->name('role_multi_delete');
        });
    });
    /**Admin management */
    Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {

            Route::get('/', 'AdminController@index')->middleware([])->name('admin_index');
            Route::get('create', 'AdminController@create')->middleware([])->name('admin_create');
            Route::get('edit/{id}', 'AdminController@create')->middleware([])->name('admin_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'admin/store', [
                'as' => 'admin_store',
                'uses' => 'AdminController@store',
            ]);
            Route::delete('delete', 'AdminController@destroyAdmin')->middleware([])->name('admin_delete');
            Route::get('admin_datatable', 'AdminController@getdata')->middleware([])->name('admin_datatable');
            Route::post('active_inactive', 'AdminController@updateStatus')->middleware([])->name('admin_active_inactive');
            Route::post('admin_multi_delete', 'AdminController@multideleteAdmin')->middleware([])->name('admin_multi_delete');
        });

    });
    /**Permission route list */
    Route::group(['prefix' => 'permission'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {

            Route::get('/', 'PermissionController@index')->middleware([])->name('permission_index');
            Route::get('permission_create', 'PermissionController@create')->middleware([])->name('permission_create');
            Route::get('edit/{id}', 'PermissionController@create')->middleware([])->name('permission_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'store', [
                'as' => 'permission_store',
                'uses' => 'PermissionController@store',
            ]);
            Route::delete('delete', 'PermissionController@destroyPermission')->middleware([])->name('permission_delete');
            Route::get('permission_datatable', 'PermissionController@getdata')->middleware([])->name('permission_datatable');
            Route::post('permission_multi_delete', 'PermissionController@multideletePermission')->middleware([])->name('permission_multi_delete');
        });
    });

    /**CMS route list */
    Route::group(['prefix' => 'CMS'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {
            Route::get('/', 'CMSController@index')->middleware(['permission:page view'])->name('cms_index');
            Route::get('create', 'CMSController@create')->middleware(['permission:page create'])->name('cms_create');
            Route::get('edit/{uuid}', 'CMSController@create')->middleware(['permission:page edit'])->name('cms_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'store', [
                'as' => 'cms_store',
                'uses' => 'CMSController@store',
            ]);
            Route::delete('delete', 'CMSController@destroyCms')->middleware(['permission:page delete'])->name('cms_delete');
            Route::post('multi_delete', 'CMSController@multideleteCMS')->middleware(['permission:page multiple delete'])->name('cms_multi_delete');
            Route::get('/cms_datatable', 'CMSController@getdata')->middleware([])->name('cms_datatable');
            Route::post('active_inactive', 'CMSController@updateStatus')->name('cms_active_inactive');
        });
    });

    /**Contact Us route list */
    Route::group(['prefix' => 'contactus'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {

            Route::get('/', 'ContactUsController@index')->middleware([])->name('contact_us_index');
            Route::get('contact_us_create', 'ContactUsController@create')->middleware([])->name('contact_us_create');
            Route::match(['post', 'PUT'], 'store', [
                'as' => 'contact_us_store',
                'uses' => 'ContactUsController@store',
            ]);
            Route::get('edit/{uuid}', 'ContactUsController@create')->middleware([])->name('contact_us_edit')->middleware('signed');
            Route::post('contact_us_multi_delete', 'ContactUsController@multideleteContactUs')->middleware([])->name('contact_us_multi_delete');
            Route::delete('delete', 'ContactUsController@destroyContactUs')->middleware([])->name('contact_us_delete');
            Route::get('contact_us_datatable', 'ContactUsController@getdata')->middleware([])->name('contact_us_datatable');
            Route::get('detail/{uuid}', 'ContactUsController@detail')->name('contact_us_detail');
        });
    });

    Route::group(['prefix' => 'websetting', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'SettingController@index')->name('web_setting_index')->middleware(['permission:web setting view']);
        Route::match(['post', 'PUT'], '/store/{id?}', 'SettingController@store')->name('general_setting_store');
    });

    /**Push Notification management */
    Route::group(['prefix' => 'notifications'], function () {
        Route::group(['middleware' => ['auth:admin']], function () {
            Route::get('/', 'PushNotificationController@index')->middleware([])->name('push_notification_index');
            Route::get('edit/{id}', 'PushNotificationController@create')->middleware([])->name('push_notification_edit')->middleware('signed');
            Route::match(['post', 'PUT'], 'role/store', [
                'as' => 'push_notification_store',
                'uses' => 'PushNotificationController@store',
            ]);
            Route::get('push_notification_datatable', 'PushNotificationController@getdata')->middleware([])->name('push_notification_datatable');
        });
    });

    /**Category management route list */
    Route::group(['prefix' => 'category', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'CategoryController@index')->name('category.index');
        Route::get('create', 'CategoryController@create')->name('category.create');
        Route::get('edit/{uuid}', 'CategoryController@create')->name('category.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'CategoryController@store')->name('category.store');
        Route::delete('delete', 'CategoryController@destroy')->name('category.delete');
        Route::get('datatable', 'CategoryController@getdata')->name('category.datatable');
        Route::post('active_inactive', 'CategoryController@updateStatus')->name('category.active_inactive');
        Route::post('multi_delete', 'CategoryController@multidelete')->name('category.multi_delete');
    });

    /**Sub Category management route list */
    Route::group(['prefix' => 'sub-category', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'SubCategoryController@index')->name('subcategory.index');
        Route::get('create', 'SubCategoryController@create')->name('subcategory.create');
        Route::get('edit/{uuid}', 'SubCategoryController@create')->name('subcategory.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'SubCategoryController@store')->name('subcategory.store');
        Route::delete('delete', 'SubCategoryController@destroy')->name('subcategory.delete');
        Route::get('datatable', 'SubCategoryController@getdata')->name('subcategory.datatable');
        Route::post('active_inactive', 'SubCategoryController@updateStatus')->name('subcategory.active_inactive');
        Route::post('multi_delete', 'SubCategoryController@multidelete')->name('subcategory.multi_delete');
    });

    /**Banner management route list */
    Route::group(['prefix' => 'banner', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'BannerController@index')->name('banner.index');
        Route::get('create', 'BannerController@create')->name('banner.create');
        Route::get('edit/{uuid}', 'BannerController@create')->name('banner.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'BannerController@store')->name('banner.store');
        Route::delete('delete', 'BannerController@destroy')->name('banner.delete');
        Route::get('datatable', 'BannerController@getdata')->name('banner.datatable');
        Route::post('active_inactive', 'BannerController@updateStatus')->name('banner.active_inactive');
        Route::post('multi_delete', 'BannerController@multidelete')->name('banner.multi_delete');
    });

    /**Product management route list */
    Route::group(['prefix' => 'product', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'ProductController@index')->name('product.index');
        Route::get('create', 'ProductController@create')->name('product.create');
        Route::get('edit/{uuid}', 'ProductController@create')->name('product.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'ProductController@store')->name('product.store');
        Route::delete('delete', 'ProductController@destroy')->name('product.delete');
        Route::get('datatable', 'ProductController@getdata')->name('product.datatable');
        Route::post('active_inactive', 'ProductController@updateStatus')->name('product.active_inactive');
        Route::post('multi_delete', 'ProductController@multidelete')->name('product.multi_delete');
        Route::post('get-sub-cat-list', 'ProductController@subCategoryList')->name('get-sub-cat-list');
    });

    /**Faq management route list */
    Route::group(['prefix' => 'faq', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'FaqController@index')->name('faq.index');
        Route::get('create', 'FaqController@create')->name('faq.create');
        Route::get('edit/{uuid}', 'FaqController@create')->name('faq.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'FaqController@store')->name('faq.store');
        Route::delete('delete', 'FaqController@destroy')->name('faq.delete');
        Route::get('datatable', 'FaqController@getdata')->name('faq.datatable');
        Route::post('active_inactive', 'FaqController@updateStatus')->name('faq.active_inactive');
        Route::post('multi_delete', 'FaqController@multidelete')->name('faq.multi_delete');
    });
    /**Email template management route list */
    Route::group(['prefix' => 'email-template', 'middleware' => ['auth:admin']], function () {
        Route::get('/', 'EmailTemplateController@index')->name('emailTemplate.index');
        Route::get('create', 'EmailTemplateController@create')->name('emailTemplate.create');
        Route::get('edit/{uuid}', 'EmailTemplateController@create')->name('emailTemplate.edit')->middleware('signed');
        Route::match(['post', 'PUT'], '/store/{id?}', 'EmailTemplateController@store')->name('emailTemplate.store');
        Route::delete('delete', 'EmailTemplateController@destroy')->name('emailTemplate.delete');
        Route::get('datatable', 'EmailTemplateController@getdata')->name('emailTemplate.datatable');
        Route::post('active_inactive', 'EmailTemplateController@updateStatus')->name('emailTemplate.active_inactive');
        Route::post('multi_delete', 'EmailTemplateController@multidelete')->name('emailTemplate.multi_delete');
    });
    /**Admin profile route list */
    Route::group([ 'prefix' => 'profile','middleware' => ['auth:admin']], function () {
        Route::get('/','AdminController@profile')->name('profile');//->middleware(['permission:profile view']);
        Route::match(['post', 'PUT'], '/store/{id}','AdminController@updateProfile')->name('profile_update');//->middleware(['permission:profile update']);
    });

});
