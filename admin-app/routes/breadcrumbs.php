<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

// admin dashboard
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});

// Profile/Account Info
Breadcrumbs::for('admin.account.info', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Profilo', route('admin.account.info'));
});

Breadcrumbs::macro('resource', function (string $name, string $title, ?string $parentName = null) {
    if ($parentName) {
        Breadcrumbs::for("{$name}.index", function (BreadcrumbTrail $trail, $model) use ($name, $title, $parentName) {
            $trail->parent("{$parentName}.show", $model);
            $trail->push($title, route("{$name}.index", $model));
        });

        Breadcrumbs::for("{$name}.create", function (BreadcrumbTrail $trail, $model) use ($name) {
            $trail->parent("{$name}.index", $model);
            $trail->push('Create', route("{$name}.create", $model));
        });

        Breadcrumbs::for("{$name}.show", function (BreadcrumbTrail $trail, $model, $item) use ($name) {
            $trail->parent("{$name}.index", $model, $item);
            if (Route::has("{$name}.show")) {
                $trail->push($item->name ?? $model, route("{$name}.show", [$model, $item]));
            } else {
                $trail->push($item->name ?? $model);
            }
        });

        Breadcrumbs::for("{$name}.edit", function (BreadcrumbTrail $trail, $model, $item) use ($name) {
            $trail->parent("{$name}.show", $model, $item);
            $trail->push('Edit', route("{$name}.edit", [$model, $item]));
        });

    } else {
        Breadcrumbs::for("{$name}.index", function (BreadcrumbTrail $trail) use ($name, $title) {
            $trail->parent('admin.dashboard');
            $trail->push($title, route("{$name}.index"));
        });

        Breadcrumbs::for("{$name}.create", function (BreadcrumbTrail $trail) use ($name) {
            $trail->parent("{$name}.index");
            $trail->push('Create', route("{$name}.create"));
        });

        Breadcrumbs::for("{$name}.show", function (BreadcrumbTrail $trail, $model) use ($name) {
            $trail->parent("{$name}.index");
            if (Route::has("$name.show")) {
                $trail->push($model->name ?? $model, route("{$name}.show", $model));
            } else {
                $trail->push($model->name ?? $model);
            }
        });

        Breadcrumbs::for("{$name}.edit", function (BreadcrumbTrail $trail, $model) use ($name) {
            $trail->parent("{$name}.show", $model);
            $trail->push('Edit', route("{$name}.edit", $model));
        });
    }
});

Breadcrumbs::resource('admin.permission', 'Permissions');
Breadcrumbs::resource('admin.role', 'Roles');
Breadcrumbs::resource('admin.user', 'Users');
Breadcrumbs::resource('admin.media', 'Media');
Breadcrumbs::resource('admin.menu', 'Menu');




Breadcrumbs::resource('admin.menu.item', 'Menu Items', 'admin.menu');
Breadcrumbs::resource('admin.category.type', 'Category Types');
Breadcrumbs::resource('admin.category.type.item', 'Items', 'admin.category.type');


Breadcrumbs::resource('admin.demo.forms', 'Forms');

// ===== BREADCRUMBS MODULI AZIENDALI =====

// Modulo Home
Breadcrumbs::for('admin.home.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Home', route('admin.home.index'));
});

Breadcrumbs::for('admin.home.notifications', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home.index');
    $trail->push('Notifiche', route('admin.home.notifications'));
});

Breadcrumbs::for('admin.home.dashboard_obiettivi', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home.index');
    $trail->push('Dashboard Obiettivi', route('admin.home.dashboard_obiettivi'));
});

// Modulo HR
Breadcrumbs::for('admin.hr.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('HR', route('admin.hr.index'));
});

Breadcrumbs::for('admin.hr.employees', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Dipendenti', route('admin.hr.employees'));
});

Breadcrumbs::for('admin.hr.employees.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.employees');
    $trail->push('Nuovo Dipendente', route('admin.hr.employees.create'));
});

Breadcrumbs::for('admin.hr.reports', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Report', route('admin.hr.reports'));
});

// Sottomenu HR
Breadcrumbs::for('admin.hr.cruscotto_lead_recruit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Cruscotto Lead Recruit', route('admin.hr.cruscotto_lead_recruit'));
});

Breadcrumbs::for('admin.hr.gara_ore', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Gara Ore', route('admin.hr.gara_ore'));
});

Breadcrumbs::for('admin.hr.gara_punti', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Gara Punti', route('admin.hr.gara_punti'));
});

Breadcrumbs::for('admin.hr.formazione', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Formazione', route('admin.hr.formazione'));
});

Breadcrumbs::for('admin.hr.stringhe', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Stringhe', route('admin.hr.stringhe'));
});

Breadcrumbs::for('admin.hr.cruscotto_assenze', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Cruscotto Assenze', route('admin.hr.cruscotto_assenze'));
});

Breadcrumbs::for('admin.hr.gestione_operatori', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Gestione Operatori', route('admin.hr.gestione_operatori'));
});

Breadcrumbs::for('admin.hr.pes', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('PES', route('admin.hr.pes'));
});

Breadcrumbs::for('admin.hr.tabella_per_mese', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Tabella per Mese', route('admin.hr.tabella_per_mese'));
});

Breadcrumbs::for('admin.hr.tabella_per_operatore', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Tabella per Operatore', route('admin.hr.tabella_per_operatore'));
});

Breadcrumbs::for('admin.hr.archivio_iban_operatori', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Archivio IBAN Operatori', route('admin.hr.archivio_iban_operatori'));
});

Breadcrumbs::for('admin.hr.import_indeed', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hr.index');
    $trail->push('Import Indeed', route('admin.hr.import_indeed'));
});

// Modulo Amministrazione
Breadcrumbs::for('admin.amministrazione.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Amministrazione', route('admin.amministrazione.index'));
});

Breadcrumbs::for('admin.amministrazione.invoices', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Fatture', route('admin.amministrazione.invoices'));
});

Breadcrumbs::for('admin.amministrazione.invoices.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.invoices');
    $trail->push('Nuova Fattura', route('admin.amministrazione.invoices.create'));
});

Breadcrumbs::for('admin.amministrazione.budget', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Budget', route('admin.amministrazione.budget'));
});

Breadcrumbs::for('admin.amministrazione.reports', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Report', route('admin.amministrazione.reports'));
});

// Sottomenu Amministrazione
Breadcrumbs::for('admin.amministrazione.pda_media', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('PDA Media', route('admin.amministrazione.pda_media'));
});

Breadcrumbs::for('admin.amministrazione.costi_stipendi', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Costi Stipendi', route('admin.amministrazione.costi_stipendi'));
});

Breadcrumbs::for('admin.amministrazione.costi_generali', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Costi Generali', route('admin.amministrazione.costi_generali'));
});

Breadcrumbs::for('admin.amministrazione.inviti_a_fatturare', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Inviti a Fatturare', route('admin.amministrazione.inviti_a_fatturare'));
});

Breadcrumbs::for('admin.amministrazione.lettere_canvass', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.amministrazione.index');
    $trail->push('Lettere Canvass', route('admin.amministrazione.lettere_canvass'));
});

// Modulo Produzione
Breadcrumbs::for('admin.produzione.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Produzione', route('admin.produzione.index'));
});

Breadcrumbs::for('admin.produzione.orders', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Ordini', route('admin.produzione.orders'));
});

Breadcrumbs::for('admin.produzione.orders.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.orders');
    $trail->push('Nuovo Ordine', route('admin.produzione.orders.create'));
});

Breadcrumbs::for('admin.produzione.quality', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Qualità', route('admin.produzione.quality'));
});

Breadcrumbs::for('admin.produzione.reports', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Report', route('admin.produzione.reports'));
});

Breadcrumbs::for('admin.produzione.kpi_target', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('KPI Target', route('admin.produzione.kpi_target'));
});

Breadcrumbs::for('admin.produzione.kpi_target.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.kpi_target');
    $trail->push('Nuovo KPI', route('admin.produzione.kpi_target.create'));
});

Breadcrumbs::for('admin.produzione.kpi_target.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.produzione.kpi_target');
    
    // Se $id è un oggetto, usa la sua proprietà id, altrimenti usa il valore diretto
    $kpiId = is_object($id) ? $id->id : $id;
    
    $trail->push('Dettaglio KPI #' . $kpiId);
});

// Esiti Conversione Committenti
Breadcrumbs::for('admin.ict.esiti_conversione.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Esiti Committenti', route('admin.ict.esiti_conversione.index'));
});

Breadcrumbs::for('admin.ict.esiti_conversione.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.esiti_conversione.index');
    $trail->push('Nuova Conversione', route('admin.ict.esiti_conversione.create'));
});

Breadcrumbs::for('admin.ict.esiti_conversione.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.ict.esiti_conversione.index');
    $esitoId = is_object($id) ? $id->id : $id;
    $trail->push('Modifica #' . $esitoId);
});

// Esiti Conversione Vendita
Breadcrumbs::for('admin.ict.esiti_vendita_conversione.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Esiti Vendita', route('admin.ict.esiti_vendita_conversione.index'));
});

Breadcrumbs::for('admin.ict.esiti_vendita_conversione.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.esiti_vendita_conversione.index');
    $trail->push('Nuova Conversione', route('admin.ict.esiti_vendita_conversione.create'));
});

Breadcrumbs::for('admin.ict.esiti_vendita_conversione.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.ict.esiti_vendita_conversione.index');
    $esitoId = is_object($id) ? $id->id : $id;
    $trail->push('Modifica #' . $esitoId);
});

// Modulo Marketing
Breadcrumbs::for('admin.marketing.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Marketing', route('admin.marketing.index'));
});

Breadcrumbs::for('admin.marketing.campaigns', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Campagne', route('admin.marketing.campaigns'));
});

Breadcrumbs::for('admin.marketing.campaigns.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.campaigns');
    $trail->push('Nuova Campagna', route('admin.marketing.campaigns.create'));
});

Breadcrumbs::for('admin.marketing.leads', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Lead', route('admin.marketing.leads'));
});

Breadcrumbs::for('admin.marketing.reports', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Report', route('admin.marketing.reports'));
});

// Modulo ICT
Breadcrumbs::for('admin.ict.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('ICT', route('admin.ict.index'));
});

Breadcrumbs::for('admin.ict.system', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Sistema', route('admin.ict.system'));
});

Breadcrumbs::for('admin.ict.users', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Utenti', route('admin.ict.users'));
});

Breadcrumbs::for('admin.ict.tickets', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Ticket', route('admin.ict.tickets'));
});

Breadcrumbs::for('admin.ict.security', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Sicurezza', route('admin.ict.security'));
});

Breadcrumbs::for('admin.ict.reports', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Report', route('admin.ict.reports'));
});

// Sottomenu Produzione
Breadcrumbs::for('admin.produzione.tabella_obiettivi', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Tabella Obiettivi', route('admin.produzione.tabella_obiettivi'));
});

Breadcrumbs::for('admin.produzione.cruscotto_produzione', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Cruscotto Produzione', route('admin.produzione.cruscotto_produzione'));
});

Breadcrumbs::for('admin.produzione.cruscotto_operatore', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Cruscotto Operatore', route('admin.produzione.cruscotto_operatore'));
});

Breadcrumbs::for('admin.produzione.cruscotto_mensile', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Cruscotto Mensile', route('admin.produzione.cruscotto_mensile'));
});

Breadcrumbs::for('admin.produzione.input_manuale', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Input Manuale', route('admin.produzione.input_manuale'));
});

Breadcrumbs::for('admin.produzione.avanzamento_mensile', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Avanzamento Mensile', route('admin.produzione.avanzamento_mensile'));
});

Breadcrumbs::for('admin.produzione.kpi_lead_quartili', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('KPI Lead Quartili', route('admin.produzione.kpi_lead_quartili'));
});

Breadcrumbs::for('admin.produzione.controllo_stato_lead', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.produzione.index');
    $trail->push('Controllo Stato Lead', route('admin.produzione.controllo_stato_lead'));
});

// Sottomenu Marketing
Breadcrumbs::for('admin.marketing.cruscotto_lead', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Cruscotto Lead', route('admin.marketing.cruscotto_lead'));
});

Breadcrumbs::for('admin.marketing.costi_invio_messaggi', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Costi Invio Messaggi', route('admin.marketing.costi_invio_messaggi'));
});

Breadcrumbs::for('admin.marketing.controllo_sms', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Controllo SMS', route('admin.marketing.controllo_sms'));
});

// Prospetto Mensile
Breadcrumbs::for('admin.marketing.prospetto_mensile.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Prospetto Mensile', route('admin.marketing.prospetto_mensile.index'));
});

Breadcrumbs::for('admin.marketing.prospetto_mensile.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.prospetto_mensile.index');
    $trail->push('Crea Prospetto', route('admin.marketing.prospetto_mensile.create'));
});

Breadcrumbs::for('admin.marketing.prospetto_mensile.view', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.marketing.prospetto_mensile.index');
    $prospettoId = is_object($id) ? $id->id : $id;
    $trail->push('Visualizza #' . $prospettoId);
});

Breadcrumbs::for('admin.marketing.prospetto_mensile.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.marketing.prospetto_mensile.index');
    $prospettoId = is_object($id) ? $id->id : $id;
    $trail->push('Modifica #' . $prospettoId);
});

// Configurazione UTM Campagne
Breadcrumbs::for('admin.marketing.configurazione_utm.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.index');
    $trail->push('Configurazione UTM', route('admin.marketing.configurazione_utm.index'));
});

Breadcrumbs::for('admin.marketing.configurazione_utm.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.marketing.configurazione_utm.index');
    $trail->push('Nuova Configurazione', route('admin.marketing.configurazione_utm.create'));
});

Breadcrumbs::for('admin.marketing.configurazione_utm.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.marketing.configurazione_utm.index');
    $configId = is_object($id) ? $id->id : $id;
    $trail->push('Modifica #' . $configId);
});

// Sottomenu ICT
Breadcrumbs::for('admin.ict.calendario', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Calendario', route('admin.ict.calendario'));
});

Breadcrumbs::for('admin.ict.stato', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Stato', route('admin.ict.stato'));
});

Breadcrumbs::for('admin.ict.categoria_utm_campagna', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Categoria UTM Campagna', route('admin.ict.categoria_utm_campagna'));
});

Breadcrumbs::for('admin.ict.aggiorna_mandati', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Aggiorna Mandati', route('admin.ict.aggiorna_mandati'));
});

Breadcrumbs::for('admin.ict.google_ads_api', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Google Ads API', route('admin.ict.google_ads_api'));
});

// Mantenimenti Bonus Incentivi
Breadcrumbs::for('admin.ict.mantenimenti_bonus_incentivi.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.index');
    $trail->push('Mantenimenti Bonus Incentivi', route('admin.ict.mantenimenti_bonus_incentivi.index'));
});

Breadcrumbs::for('admin.ict.mantenimenti_bonus_incentivi.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.ict.mantenimenti_bonus_incentivi.index');
    $trail->push('Nuovo Mantenimento', route('admin.ict.mantenimenti_bonus_incentivi.create'));
});

Breadcrumbs::for('admin.ict.mantenimenti_bonus_incentivi.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.ict.mantenimenti_bonus_incentivi.index');
    $trail->push('Modifica Mantenimento', route('admin.ict.mantenimenti_bonus_incentivi.edit', $id));
});
