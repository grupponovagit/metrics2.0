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
