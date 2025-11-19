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
        Route::get('/dashboard-obiettivi', 'HomeController@dashboardObiettivi')->name('dashboard_obiettivi');
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
        
        // Sottomenu HR
        Route::get('/cruscotto-lead-recruit', 'HRController@cruscottoLeadRecruit')->name('cruscotto_lead_recruit');
        Route::get('/gara-ore', 'HRController@garaOre')->name('gara_ore');
        Route::get('/gara-punti', 'HRController@garaPunti')->name('gara_punti');
        Route::get('/formazione', 'HRController@formazione')->name('formazione');
        Route::get('/stringhe', 'HRController@stringhe')->name('stringhe');
        Route::get('/cruscotto-assenze', 'HRController@cruscottoAssenze')->name('cruscotto_assenze');
        Route::get('/gestione-operatori', 'HRController@gestioneOperatori')->name('gestione_operatori');
        Route::get('/pes', 'HRController@pes')->name('pes');
        Route::get('/tabella-per-mese', 'HRController@tabellaPerMese')->name('tabella_per_mese');
        Route::get('/tabella-per-operatore', 'HRController@tabellaPerOperatore')->name('tabella_per_operatore');
        Route::get('/archivio-iban-operatori', 'HRController@archivioIbanOperatori')->name('archivio_iban_operatori');
        Route::get('/import-indeed', 'HRController@importIndeed')->name('import_indeed');
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
        
        // Sottomenu Amministrazione
        Route::get('/pda-media', 'AmministrazioneController@pdaMedia')->name('pda_media');
        Route::get('/costi-stipendi', 'AmministrazioneController@costiStipendi')->name('costi_stipendi');
        Route::get('/costi-generali', 'AmministrazioneController@costiGenerali')->name('costi_generali');
        Route::get('/inviti-a-fatturare', 'AmministrazioneController@invitiAFatturare')->name('inviti_a_fatturare');
        Route::get('/lettere-canvass', 'AmministrazioneController@lettereCanvass')->name('lettere_canvass');
    });

    // Modulo Produzione - Solo per ruoli autorizzati
    Route::group([
        'prefix' => 'produzione',
        'as' => 'produzione.',
        'middleware' => 'module.access:produzione'
    ], function () {
        Route::get('/', 'ProduzioneController@index')->name('index');
        Route::get('/reports', 'ProduzioneController@reports')->name('reports');
        
        // Sottomenu Produzione
        Route::get('/tabella-obiettivi', 'ProduzioneController@tabellaObiettivi')->name('tabella_obiettivi');
        Route::get('/cruscotto-produzione', 'ProduzioneController@cruscottoProduzione')->name('cruscotto_produzione');
        
        // API per filtri dinamici cruscotto produzione
        Route::get('/get-sedi', 'ProduzioneController@getSedi')->name('get_sedi');
        Route::get('/get-campagne', 'ProduzioneController@getCampagne')->name('get_campagne');
        
        Route::get('/cruscotto-operatore', 'ProduzioneController@cruscottoOperatore')->name('cruscotto_operatore');
        Route::get('/input-manuale', 'ProduzioneController@inputManuale')->name('input_manuale');
        Route::get('/avanzamento-mensile', 'ProduzioneController@avanzamentoMensile')->name('avanzamento_mensile');
        Route::get('/kpi-lead-quartili', 'ProduzioneController@kpiLeadQuartili')->name('kpi_lead_quartili');
        Route::get('/controllo-stato-lead', 'ProduzioneController@controlloStatoLead')->name('controllo_stato_lead');
        
        // KPI Target Mensili
        Route::get('/kpi-target', 'ProduzioneController@kpiTarget')->name('kpi_target');
        
        // Filtri concatenati KPI Target (PRIMA delle route dinamiche)
        Route::get('/kpi-target/get-sedi', 'ProduzioneController@getSediKpiTarget')->name('kpi_target.get_sedi');
        Route::get('/kpi-target/get-macro-campagne', 'ProduzioneController@getMacroCampagneKpiTarget')->name('kpi_target.get_macro_campagne');
        Route::get('/kpi-target/get-nomi-kpi', 'ProduzioneController@getNomiKpiTarget')->name('kpi_target.get_nomi_kpi');
        Route::get('/kpi-target/get-tipologie', 'ProduzioneController@getTipologieObiettivoKpiTarget')->name('kpi_target.get_tipologie');
        
        Route::get('/kpi-target/create', 'ProduzioneController@createKpiTarget')->name('kpi_target.create');
        Route::post('/kpi-target/store', 'ProduzioneController@storeKpiTarget')->name('kpi_target.store');
        Route::post('/kpi-target/inizializza-mese', 'ProduzioneController@inizializzaMese')->name('kpi_target.inizializza_mese');
        Route::get('/kpi-target/{id}', 'ProduzioneController@showKpiTarget')->name('kpi_target.show');
        Route::post('/kpi-target/{id}/update-field', 'ProduzioneController@updateKpiField')->name('kpi_target.update_field');
        Route::post('/kpi-target/{id}/update-variazione', 'ProduzioneController@updateKpiVariazione')->name('kpi_target.update_variazione');
        Route::post('/kpi-target/update', 'ProduzioneController@updateKpiTarget')->name('kpi_target.update');
        Route::delete('/kpi-target/{id}', 'ProduzioneController@deleteKpiTarget')->name('kpi_target.delete');
        Route::post('/kpi-target/bulk-delete', 'ProduzioneController@bulkDeleteKpiTarget')->name('kpi_target.bulk_delete');
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
        
        // Sottomenu Marketing
        Route::get('/cruscotto-lead', 'MarketingController@cruscottoLead')->name('cruscotto_lead');
        
        // API per filtri dinamici cruscotto lead
        Route::get('/cruscotto-lead/get-provenienze', 'MarketingController@getProvenienze')->name('cruscotto_lead.get_provenienze');
        Route::get('/cruscotto-lead/get-campagne', 'MarketingController@getCampagne')->name('cruscotto_lead.get_campagne');
        
        Route::get('/costi-invio-messaggi', 'MarketingController@costiInvioMessaggi')->name('costi_invio_messaggi');
        Route::get('/controllo-sms', 'MarketingController@controlloSms')->name('controllo_sms');
        
        // Prospetto Mensile
        Route::get('/prospetto-mensile', 'MarketingController@prospettoMensile')->name('prospetto_mensile.index');
        Route::get('/prospetto-mensile/create', 'MarketingController@prospettoMensileCreate')->name('prospetto_mensile.create');
        Route::post('/prospetto-mensile', 'MarketingController@prospettoMensileStore')->name('prospetto_mensile.store');
        Route::get('/prospetto-mensile/{id}', 'MarketingController@prospettoMensileView')->name('prospetto_mensile.view');
        Route::get('/prospetto-mensile/{id}/edit', 'MarketingController@prospettoMensileEdit')->name('prospetto_mensile.edit');
        Route::put('/prospetto-mensile/{id}', 'MarketingController@prospettoMensileUpdate')->name('prospetto_mensile.update');
        Route::delete('/prospetto-mensile/{id}', 'MarketingController@prospettoMensileDestroy')->name('prospetto_mensile.destroy');
        Route::post('/prospetto-mensile/{id}/toggle-attivo', 'MarketingController@prospettoMensileToggleAttivo')->name('prospetto_mensile.toggle_attivo');
        
        // Configurazione UTM Campagne
        Route::get('/configurazione-utm', 'ConfigurazioneUtmController@index')->name('configurazione_utm.index');
        Route::get('/configurazione-utm/create', 'ConfigurazioneUtmController@create')->name('configurazione_utm.create');
        Route::post('/configurazione-utm', 'ConfigurazioneUtmController@store')->name('configurazione_utm.store');
        Route::get('/configurazione-utm/{id}/edit', 'ConfigurazioneUtmController@edit')->name('configurazione_utm.edit');
        Route::put('/configurazione-utm/{id}', 'ConfigurazioneUtmController@update')->name('configurazione_utm.update');
        Route::delete('/configurazione-utm/{id}', 'ConfigurazioneUtmController@destroy')->name('configurazione_utm.destroy');
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
        
        // Sottomenu ICT
        Route::get('/calendario', 'ICTController@calendario')->name('calendario');
        Route::post('/calendario/update', 'ICTController@updateCalendario')->name('calendario.update');
        Route::post('/calendario/add-festivo', 'ICTController@addFestivo')->name('calendario.add_festivo');
        Route::post('/calendario/add-eccezione-mandato', 'ICTController@addEccezioneMandato')->name('calendario.add_eccezione_mandato');
        Route::delete('/calendario/{id}', 'ICTController@deleteGiorno')->name('calendario.delete');
        
        Route::get('/stato', 'ICTController@stato')->name('stato');
        Route::get('/categoria-utm-campagna', 'ICTController@categoriaUtmCampagna')->name('categoria_utm_campagna');
        Route::get('/aggiorna-mandati', 'ICTController@aggiornaMandati')->name('aggiorna_mandati');
        
        // Gestione Conversione Esiti Committenti
        Route::get('/esiti-conversione', 'ICTController@esitiConversione')->name('esiti_conversione.index');
        Route::get('/esiti-conversione/create', 'ICTController@createEsitoConversione')->name('esiti_conversione.create');
        Route::post('/esiti-conversione/store', 'ICTController@storeEsitoConversione')->name('esiti_conversione.store');
        Route::get('/esiti-conversione/{id}/edit', 'ICTController@editEsitoConversione')->name('esiti_conversione.edit');
        Route::put('/esiti-conversione/{id}', 'ICTController@updateEsitoConversione')->name('esiti_conversione.update');
        Route::delete('/esiti-conversione/{id}', 'ICTController@destroyEsitoConversione')->name('esiti_conversione.destroy');
        Route::post('/esiti-conversione/bulk-delete', 'ICTController@bulkDeleteEsitoConversione')->name('esiti_conversione.bulk_delete');
        
        // Gestione Conversione Esiti Vendita
        Route::get('/esiti-vendita-conversione', 'ICTController@esitiVenditaConversione')->name('esiti_vendita_conversione.index');
        Route::get('/esiti-vendita-conversione/create', 'ICTController@createEsitoVenditaConversione')->name('esiti_vendita_conversione.create');
        Route::post('/esiti-vendita-conversione/store', 'ICTController@storeEsitoVenditaConversione')->name('esiti_vendita_conversione.store');
        Route::get('/esiti-vendita-conversione/{id}/edit', 'ICTController@editEsitoVenditaConversione')->name('esiti_vendita_conversione.edit');
        Route::put('/esiti-vendita-conversione/{id}', 'ICTController@updateEsitoVenditaConversione')->name('esiti_vendita_conversione.update');
        Route::delete('/esiti-vendita-conversione/{id}', 'ICTController@destroyEsitoVenditaConversione')->name('esiti_vendita_conversione.destroy');
        Route::post('/esiti-vendita-conversione/bulk-delete', 'ICTController@bulkDeleteEsitoVenditaConversione')->name('esiti_vendita_conversione.bulk_delete');
        
        // Google Ads API - Gestione Autenticazioni
        Route::get('/google-ads-api', 'GoogleAdsAuthController@index')->name('google_ads_api');
    });

});
