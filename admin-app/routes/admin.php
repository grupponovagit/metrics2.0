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
        return redirect()->route('admin.home.index');
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
    
    // Profile Routes (redirect to account info)
    Route::get('profile', function () {
        return redirect()->route('admin.account.info');
    })->name('profile.edit');

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

    // Consent Management Routes
    Route::resource('consents', 'ConsentController');

    // ===== MODULI AZIENDALI =====
    
    // Modulo Home - Accessibile a tutti gli utenti autenticati
    Route::group([
        'prefix' => 'home',
        'as' => 'home.',
    ], function () {
        Route::get('/', 'HomeController@index')->name('index');
        Route::get('/notifications', 'HomeController@notifications')->name('notifications');
    });

    // Modulo HR - Solo per ruoli autorizzati
    Route::group([
        'prefix' => 'hr',
        'as' => 'hr.',
        'middleware' => 'module.access:hr'
    ], function () {
        Route::get('/', 'HRController@index')->name('index');
        Route::get('/employees', 'HRController@employees')->name('employees');
        Route::get('/employees/create', 'HRController@createEmployee')->name('employees.create');
        Route::post('/employees', 'HRController@storeEmployee')->name('employees.store');
        Route::get('/reports', 'HRController@reports')->name('reports');
    });

    // Modulo Amministrazione - Solo per ruoli autorizzati
    Route::group([
        'prefix' => 'amministrazione',
        'as' => 'amministrazione.',
        'middleware' => 'module.access:amministrazione'
    ], function () {
        Route::get('/', 'AmministrazioneController@index')->name('index');
        Route::get('/invoices', 'AmministrazioneController@invoices')->name('invoices');
        Route::get('/invoices/create', 'AmministrazioneController@createInvoice')->name('invoices.create');
        Route::post('/invoices', 'AmministrazioneController@storeInvoice')->name('invoices.store');
        Route::get('/budget', 'AmministrazioneController@budget')->name('budget');
        Route::get('/reports', 'AmministrazioneController@reports')->name('reports');
    });

    // Modulo Produzione - Solo per ruoli autorizzati
    Route::group([
        'prefix' => 'produzione',
        'as' => 'produzione.',
        'middleware' => 'module.access:produzione'
    ], function () {
        Route::get('/', 'ProduzioneController@index')->name('index');
        Route::get('/orders', 'ProduzioneController@orders')->name('orders');
        Route::get('/orders/create', 'ProduzioneController@createOrder')->name('orders.create');
        Route::post('/orders', 'ProduzioneController@storeOrder')->name('orders.store');
        Route::get('/quality', 'ProduzioneController@quality')->name('quality');
        Route::get('/reports', 'ProduzioneController@reports')->name('reports');
    });

    // Modulo Marketing - Solo per ruoli autorizzati
    Route::group([
        'prefix' => 'marketing',
        'as' => 'marketing.',
        'middleware' => 'module.access:marketing'
    ], function () {
        Route::get('/', 'MarketingController@index')->name('index');
        Route::get('/campaigns', 'MarketingController@campaigns')->name('campaigns');
        Route::get('/campaigns/create', 'MarketingController@createCampaign')->name('campaigns.create');
        Route::post('/campaigns', 'MarketingController@storeCampaign')->name('campaigns.store');
        Route::get('/leads', 'MarketingController@leads')->name('leads');
        Route::get('/reports', 'MarketingController@reports')->name('reports');
    });

    // Modulo ICT - Solo per ruoli tecnici
    Route::group([
        'prefix' => 'ict',
        'as' => 'ict.',
        'middleware' => 'module.access:ict'
    ], function () {
        Route::get('/', 'ICTController@index')->name('index');
        Route::get('/system', 'ICTController@system')->name('system');
        Route::get('/users', 'ICTController@users')->name('users');
        Route::get('/tickets', 'ICTController@tickets')->name('tickets');
        Route::get('/security', 'ICTController@security')->name('security');
        Route::get('/reports', 'ICTController@reports')->name('reports');
    });

});
