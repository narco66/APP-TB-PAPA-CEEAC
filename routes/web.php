<?php

use App\Http\Controllers\ActionPrioritaireController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\IndicateurController;
use App\Http\Controllers\LibelleMetierController;
use App\Http\Controllers\ObjectifImmediatsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConfigRbmController;
use App\Http\Controllers\ParametreAlerteController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ParametreDroitsController;
use App\Http\Controllers\ParametrePapaController;
use App\Http\Controllers\ParametreSauvegardeController;
use App\Http\Controllers\ParametreTechniqueController;
use App\Http\Controllers\ParametreWorkflowController;
use App\Http\Controllers\PapaController;
use App\Http\Controllers\GeneratedReportController;
use App\Http\Controllers\ReportDashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\ResultatAttenduController;
use App\Http\Controllers\RisqueController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\Website\PageController;
use App\Models\Direction;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// â”€â”€ Site institutionnel public â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::prefix('')->name('website.')->group(function () {
    Route::get('/',                       [PageController::class, 'home'])->name('home');
    Route::get('/a-propos',               [PageController::class, 'aPropos'])->name('a-propos');
    Route::get('/historique',             [PageController::class, 'historique'])->name('historique');
    Route::get('/vision-mission',         [PageController::class, 'visionMission'])->name('vision-mission');
    Route::get('/organes',                [PageController::class, 'organes'])->name('organes');
    Route::get('/mot-du-president',       [PageController::class, 'president'])->name('president');
    Route::get('/etats-membres',          [PageController::class, 'etatsMembres'])->name('etats-membres');
    Route::get('/domaines/{slug}',        [PageController::class, 'domaine'])->name('domaine');
    Route::get('/programmes',             [PageController::class, 'programmes'])->name('programmes');
    Route::get('/actualites',             [PageController::class, 'actualites'])->name('actualites');
    Route::get('/actualites/{slug}',      [PageController::class, 'actualite'])->name('actualite');
    Route::get('/publications',           [PageController::class, 'publications'])->name('publications');
    Route::get('/evenements',             [PageController::class, 'evenements'])->name('evenements');
    Route::get('/communiques',            [PageController::class, 'communiques'])->name('communiques');
    Route::get('/contact',                [PageController::class, 'contact'])->name('contact');
    Route::post('/contact',               [PageController::class, 'contactStore'])->name('contact.store');
});

// â”€â”€ App: redirection depuis /app â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::get('/app', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// â”€â”€ Authentification â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware('guest')->group(function () {
    Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// â”€â”€ Zone protÃ©gÃ©e â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // â”€â”€ PAPA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('papas', PapaController::class);
    Route::prefix('papas/{papa}')->name('papas.')->group(function () {
        Route::get('audit', [PapaController::class, 'audit'])->name('audit');
        Route::post('soumettre', [PapaController::class, 'soumettre'])->name('soumettre');
        Route::post('valider',   [PapaController::class, 'valider'])->name('valider');
        Route::post('rejeter',   [PapaController::class, 'rejeter'])->name('rejeter');
        Route::post('archiver',  [PapaController::class, 'archiver'])->name('archiver');
        Route::post('cloner',    [PapaController::class, 'cloner'])->name('cloner');
        Route::post('recalculer', [PapaController::class, 'recalculer'])->name('recalculer');
    });

    // â”€â”€ ChaÃ®ne hiÃ©rarchique PAPA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('actions-prioritaires', ActionPrioritaireController::class);
    Route::resource('objectifs-immediats',  ObjectifImmediatsController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::resource('resultats-attendus',   ResultatAttenduController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // â”€â”€ ActivitÃ©s â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('activites', ActiviteController::class);
    Route::get('activites-gantt', [ActiviteController::class, 'gantt'])->name('activites.gantt');
    Route::post('activites/{activite}/avancement', [ActiviteController::class, 'mettreAJourAvancement'])
        ->name('activites.avancement');

    // â”€â”€ Indicateurs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('indicateurs', IndicateurController::class);
    Route::post('indicateurs/{indicateur}/valeurs', [IndicateurController::class, 'saisirValeur'])
        ->name('indicateurs.saisir-valeur');
    Route::post('indicateurs/valeurs/{valeur}/valider', [IndicateurController::class, 'validerValeur'])
        ->name('indicateurs.valider-valeur');

    // â”€â”€ Documents / GED â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('documents/export-audit', [DocumentController::class, 'exportAudit'])
        ->name('documents.export-audit');
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
    Route::post('documents/{document}/valider', [DocumentController::class, 'valider'])
        ->name('documents.valider');
    Route::post('documents/{document}/archiver', [DocumentController::class, 'archiver'])
        ->name('documents.archiver');

    // â”€â”€ API interne (JSON) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('/api/directions/{direction}/services', function (Direction $direction) {
        return $direction->services()->actif()->orderBy('libelle')->get(['id', 'libelle']);
    })->name('api.direction.services');

    Route::get('/api/papa/{papa}/actions-prioritaires', function (\App\Models\Papa $papa) {
        return $papa->actionsPrioritaires()->orderBy('ordre')->get(['id', 'code', 'libelle']);
    })->name('api.papa.actions-prioritaires');

    Route::get('/api/papa/{papa}/objectifs-immediats', function (\App\Models\Papa $papa) {
        return \App\Models\ObjectifImmediats::whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $papa->id))
            ->orderBy('code')->get(['id', 'code', 'libelle']);
    })->name('api.papa.objectifs-immediats');

    // â”€â”€ Alertes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('alertes', AlerteController::class)->only(['index', 'show']);
    Route::post('alertes/{alerte}/traiter', [AlerteController::class, 'traiter'])
        ->name('alertes.traiter');
    Route::post('alertes/{alerte}/escalader', [AlerteController::class, 'escalader'])
        ->name('alertes.escalader');
    Route::post('papas/{papa}/alertes/generer', [AlerteController::class, 'generer'])
        ->name('papas.alertes.generer');

    // â”€â”€ Rapports â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('reporting')->name('reports.')->group(function () {
        Route::get('dashboard', [ReportDashboardController::class, 'index'])->name('dashboard');
        Route::post('generate/{definition}', [ReportDashboardController::class, 'generate'])->name('generate');
        Route::get('library', [GeneratedReportController::class, 'index'])->name('library.index');
        Route::get('library/{generatedReport}', [GeneratedReportController::class, 'show'])->name('library.show');
        Route::get('library/{generatedReport}/download', [GeneratedReportController::class, 'download'])->name('library.download');
        Route::post('library/{generatedReport}/retry', [GeneratedReportController::class, 'retry'])->name('library.retry');
    });

    Route::resource('rapports', RapportController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('rapports/{rapport}/valider', [RapportController::class, 'valider'])->name('rapports.valider');
    Route::post('rapports/{rapport}/publier', [RapportController::class, 'publier'])->name('rapports.publier');
    Route::get('rapports/{rapport}/export-pdf', [RapportController::class, 'exportPdf'])->name('rapports.export-pdf');
    Route::get('papas/{papa}/export-excel',    [RapportController::class, 'exportExcel'])->name('rapports.export-excel');
    Route::get('papas/{papa}/export-pdf',      [RapportController::class, 'exportPapaPdf'])->name('rapports.export-papa-pdf');

    // Workflow institutionnel
    Route::get('workflows', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('workflows/{workflow}', [WorkflowController::class, 'show'])->name('workflows.show');
    Route::get('workflows/{workflow}/audit', [WorkflowController::class, 'audit'])->name('workflows.audit');
    Route::post('papas/{papa}/workflow/demarrer', [WorkflowController::class, 'demarrerPapa'])->name('workflows.demarrer-papa');
    Route::post('workflows/{workflow}/approuver', [WorkflowController::class, 'approuver'])->name('workflows.approuver');
    Route::post('workflows/{workflow}/rejeter', [WorkflowController::class, 'rejeter'])->name('workflows.rejeter');
    Route::post('workflows/{workflow}/commenter', [WorkflowController::class, 'commenter'])->name('workflows.commenter');

    // DÃ©cisions et arbitrages
    Route::resource('decisions', DecisionController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('decisions/{decision}/audit', [DecisionController::class, 'audit'])->name('decisions.audit');
    Route::post('decisions/{decision}/valider', [DecisionController::class, 'valider'])->name('decisions.valider');
    Route::post('decisions/{decision}/executer', [DecisionController::class, 'executer'])->name('decisions.executer');
    Route::post('decisions/{decision}/rattacher-document', [DecisionController::class, 'rattacherDocument'])->name('decisions.rattacher-document');

    // â”€â”€ Budget â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('papas/{papa}')->name('budgets.')->group(function () {
        Route::get('budget',              [BudgetController::class, 'index'])->name('index');
        Route::get('budget/create',       [BudgetController::class, 'create'])->name('create');
        Route::post('budget',             [BudgetController::class, 'store'])->name('store');
        Route::get('budget/{budget}/edit',[BudgetController::class, 'edit'])->name('edit');
        Route::put('budget/{budget}',     [BudgetController::class, 'update'])->name('update');
        Route::delete('budget/{budget}',  [BudgetController::class, 'destroy'])->name('destroy');
    });

    // â”€â”€ Risques â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('papas/{papa}')->name('risques.')->group(function () {
        Route::get('risques',              [RisqueController::class, 'index'])->name('index');
        Route::get('risques/create',       [RisqueController::class, 'create'])->name('create');
        Route::post('risques',             [RisqueController::class, 'store'])->name('store');
        Route::get('risques/{risque}/edit',[RisqueController::class, 'edit'])->name('edit');
        Route::put('risques/{risque}',     [RisqueController::class, 'update'])->name('update');
        Route::delete('risques/{risque}',  [RisqueController::class, 'destroy'])->name('destroy');
    });

    // â”€â”€ Administration â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('audit-log', [AdminController::class, 'auditLog'])->name('audit-log');
        Route::get('audit-events', [AdminController::class, 'auditEvents'])->name('audit-events');
        Route::get('audit-events/export', [AdminController::class, 'exportAuditEvents'])->name('audit-events.export');
        Route::get('notification-rules', [AdminController::class, 'notificationRules'])->name('notification-rules');
        Route::put('notification-rules/{notificationRule}', [AdminController::class, 'updateNotificationRule'])->name('notification-rules.update');

        // Structure organisationnelle (DÃ©partements / Directions / Services)
        Route::prefix('structure')->name('structure.')->group(function () {
            Route::get('departements',                     [StructureController::class, 'departements'])->name('departements');
            Route::get('departements/create',              [StructureController::class, 'departementCreate'])->name('departements.create');
            Route::post('departements',                    [StructureController::class, 'departementStore'])->name('departements.store');
            Route::get('departements/{departement}/edit',  [StructureController::class, 'departementEdit'])->name('departements.edit');
            Route::put('departements/{departement}',       [StructureController::class, 'departementUpdate'])->name('departements.update');

            Route::get('directions',                       [StructureController::class, 'directions'])->name('directions');
            Route::get('directions/create',                [StructureController::class, 'directionCreate'])->name('directions.create');
            Route::post('directions',                      [StructureController::class, 'directionStore'])->name('directions.store');
            Route::get('directions/{direction}/edit',      [StructureController::class, 'directionEdit'])->name('directions.edit');
            Route::put('directions/{direction}',           [StructureController::class, 'directionUpdate'])->name('directions.update');

            Route::get('services',                         [StructureController::class, 'services'])->name('services');
            Route::get('services/create',                  [StructureController::class, 'serviceCreate'])->name('services.create');
            Route::post('services',                        [StructureController::class, 'serviceStore'])->name('services.store');
            Route::get('services/{service}/edit',          [StructureController::class, 'serviceEdit'])->name('services.edit');
            Route::put('services/{service}',               [StructureController::class, 'serviceUpdate'])->name('services.update');
        });

        Route::prefix('utilisateurs')->name('utilisateurs.')->group(function () {
            Route::get('/',                   [AdminController::class, 'index'])->name('index');
            Route::get('create',              [AdminController::class, 'create'])->name('create');
            Route::post('/',                  [AdminController::class, 'store'])->name('store');
            Route::get('{user}/edit',         [AdminController::class, 'edit'])->name('edit');
            Route::put('{user}',              [AdminController::class, 'update'])->name('update');
            Route::delete('{user}',           [AdminController::class, 'destroy'])->name('destroy');
            Route::post('{user}/toggle-actif',[AdminController::class, 'toggleActif'])->name('toggle-actif');
            Route::post('{user}/restore',     [AdminController::class, 'restore'])
                ->withTrashed()->name('restore');
        });
    });

    // â”€â”€ ParamÃ¨tres â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/',                         [ParametreController::class, 'hub'])->name('hub');
        Route::get('generaux',                  [ParametreController::class, 'generaux'])->name('generaux');
        Route::post('generaux',                 [ParametreController::class, 'saveGeneraux'])->name('generaux.save');
        Route::post('maintenance',              [ParametreController::class, 'toggleMaintenance'])->name('toggle-maintenance');
        Route::get('journal',                   [ParametreController::class, 'journal'])->name('journal');

        // Gestion PAPA
        Route::prefix('papa')->name('papa.')->group(function () {
            Route::get('/',                         [ParametrePapaController::class, 'index'])->name('index');
            Route::post('{papa}/activer',            [ParametrePapaController::class, 'activer'])->name('activer');
            Route::post('{papa}/verrouiller',        [ParametrePapaController::class, 'verrouiller'])->name('verrouiller');
            Route::post('{papa}/deverrouiller',      [ParametrePapaController::class, 'deverrouiller'])->name('deverrouiller');
            Route::post('{papa}/archiver',           [ParametrePapaController::class, 'archiver'])->name('archiver');
            Route::get('archives',                   [ParametrePapaController::class, 'archives'])->name('archives');
        });

        // RÃ©fÃ©rentiels
        Route::prefix('referentiels')->name('referentiels.')->group(function () {
            Route::get('/',                          [ReferentielController::class, 'index'])->name('index');
            Route::get('{type}',                     [ReferentielController::class, 'liste'])->name('liste');
            Route::post('{type}',                    [ReferentielController::class, 'store'])->name('store');
            Route::put('{type}/{referentiel}',       [ReferentielController::class, 'update'])->name('update');
            Route::post('{type}/{referentiel}/toggle',[ReferentielController::class, 'toggle'])->name('toggle');
            Route::delete('{type}/{referentiel}',    [ReferentielController::class, 'destroy'])->name('destroy');
            Route::post('{type}/reordonner',         [ReferentielController::class, 'reordonner'])->name('reordonner');
        });

        // LibellÃ©s mÃ©tier
        Route::prefix('libelles')->name('libelles.')->group(function () {
            Route::get('/',                          [LibelleMetierController::class, 'index'])->name('index');
            Route::put('{libelle}',                  [LibelleMetierController::class, 'update'])->name('update');
            Route::post('{module}/reinitialiser',    [LibelleMetierController::class, 'reinitialiser'])->name('reinitialiser');
        });

        // RBM
        Route::prefix('rbm')->name('rbm.')->group(function () {
            Route::get('/',     [ConfigRbmController::class, 'index'])->name('index');
            Route::post('/',    [ConfigRbmController::class, 'save'])->name('save');
        });

        // Alertes & Notifications
        Route::prefix('alertes')->name('alertes.')->group(function () {
            Route::get('/',                             [ParametreAlerteController::class, 'index'])->name('index');
            Route::post('seuils',                       [ParametreAlerteController::class, 'saveSeuils'])->name('seuils.save');
            Route::put('rules/{rule}',                  [ParametreAlerteController::class, 'updateRule'])->name('rules.update');
            Route::post('rules/{rule}/toggle',          [ParametreAlerteController::class, 'toggleRule'])->name('rules.toggle');
        });

        // Workflows
        Route::prefix('workflows')->name('workflows.')->group(function () {
            Route::get('/',                                             [ParametreWorkflowController::class, 'index'])->name('index');
            Route::get('{definition}',                                  [ParametreWorkflowController::class, 'edit'])->name('edit');
            Route::put('{definition}',                                  [ParametreWorkflowController::class, 'update'])->name('update');
            Route::post('{definition}/toggle',                          [ParametreWorkflowController::class, 'toggleDefinition'])->name('toggle');
            Route::put('{definition}/steps/{step}',                     [ParametreWorkflowController::class, 'updateStep'])->name('steps.update');
        });

        // Droits & RÃ´les
        Route::prefix('droits')->name('droits.')->group(function () {
            Route::get('/',                         [ParametreDroitsController::class, 'index'])->name('index');
            Route::get('matrice',                   [ParametreDroitsController::class, 'matrice'])->name('matrice');
            Route::get('roles/{role}',              [ParametreDroitsController::class, 'show'])->name('roles.show');
            Route::put('roles/{role}',              [ParametreDroitsController::class, 'updateRole'])->name('roles.update');
            Route::post('users/{user}/toggle',      [ParametreDroitsController::class, 'toggleUser'])->name('users.toggle');
        });

        // ParamÃ¨tres techniques
        Route::prefix('technique')->name('technique.')->group(function () {
            Route::get('/',     [ParametreTechniqueController::class, 'index'])->name('index');
            Route::post('/',    [ParametreTechniqueController::class, 'save'])->name('save');
        });

        // Sauvegardes
        Route::prefix('sauvegardes')->name('sauvegardes.')->group(function () {
            Route::get('/',                     [ParametreSauvegardeController::class, 'index'])->name('index');
            Route::post('exporter/{type}',      [ParametreSauvegardeController::class, 'exporter'])->name('exporter');
            Route::post('importer',             [ParametreSauvegardeController::class, 'importer'])->name('importer');
        });
    });
});
