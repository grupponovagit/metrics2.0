<?php

use App\Http\Middleware\HasAccessAdmin;
use Illuminate\Support\Facades\Route;


Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => config('admin.prefix'),
    'middleware' => ['auth', 'verified', HasAccessAdmin::class],
    'as' => 'admin.',
], function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // User Management Routes
    Route::resource('user', 'UserController');
    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');

    // Media Management Routes
    Route::resource('media', 'MediaController');

    // Menu Management Routes
    Route::resource('menu', 'MenuController')->except([
        'show',
    ]);
    Route::resource('menu.item', 'MenuItemController')->except([
        'show',
    ]);

    // Category Management Routes
    Route::group([
        'prefix' => 'category',
        'as' => 'category.',
    ], function () {
        Route::resource('type', 'CategoryTypeController')->except([
            'show',
        ]);
        Route::resource('type.item', 'CategoryController')->except([
            'show',
        ]);
    });

    // Providers


    // Account Info Routes
    Route::get('edit-account-info', 'UserController@accountInfo')->name('account.info');
    Route::post('edit-account-info', 'UserController@accountInfoStore')->name('account.info.store');
    Route::post('change-password', 'UserController@changePasswordStore')->name('account.password.store');

    // Demo Routes
    Route::group([
        'prefix' => 'demo',
        'as' => 'demo.',
    ], function () {
        Route::resource('forms', 'DemoFormsController')->except([
            'show',
            'edit',
            'update',
        ]);
    });

    // Custom Routes for New Controllers

    // Notification Management Routes
    Route::resource('notifications', 'NotificationController');

    // Consent Management Routes
    Route::resource('consents', 'ConsentController');

});
