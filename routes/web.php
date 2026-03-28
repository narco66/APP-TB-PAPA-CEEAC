<?php

use App\Http\Controllers\ActionPrioritaireController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\IndicateurController;
use App\Http\Controllers\ObjectifImmediatsController;
use App\Http\Controllers\PapaController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ResultatAttenduController;
use App\Http\Controllers\RisqueController;
use App\Http\Controllers\Website\PageController;
use App\Models\Direction;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ── Site institutionnel public ───────────────────────────────────────────
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

// ── App: redirection depuis /app ─────────────────────────────────────────
Route::get('/app', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ── Authentification ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Zone protégée ────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── PAPA ──────────────────────────────────────────────────────────────
    Route::resource('papas', PapaController::class);
    Route::prefix('papas/{papa}')->name('papas.')->group(function () {
        Route::post('soumettre', [PapaController::class, 'soumettre'])->name('soumettre');
        Route::post('valider',   [PapaController::class, 'valider'])->name('valider');
        Route::post('rejeter',   [PapaController::class, 'rejeter'])->name('rejeter');
        Route::post('archiver',  [PapaController::class, 'archiver'])->name('archiver');
        Route::post('cloner',    [PapaController::class, 'cloner'])->name('cloner');
        Route::post('recalculer', [PapaController::class, 'recalculer'])->name('recalculer');
    });

    // ── Chaîne hiérarchique PAPA ──────────────────────────────────────────
    Route::resource('actions-prioritaires', ActionPrioritaireController::class);
    Route::resource('objectifs-immediats',  ObjectifImmediatsController::class)
        ->only(['create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::resource('resultats-attendus',   ResultatAttenduController::class)
        ->only(['create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ── Activités ─────────────────────────────────────────────────────────
    Route::resource('activites', ActiviteController::class);
    Route::get('activites-gantt', [ActiviteController::class, 'gantt'])->name('activites.gantt');
    Route::post('activites/{activite}/avancement', [ActiviteController::class, 'mettreAJourAvancement'])
        ->name('activites.avancement');

    // ── Indicateurs ───────────────────────────────────────────────────────
    Route::resource('indicateurs', IndicateurController::class);
    Route::post('indicateurs/{indicateur}/valeurs', [IndicateurController::class, 'saisirValeur'])
        ->name('indicateurs.saisir-valeur');
    Route::post('indicateurs/valeurs/{valeur}/valider', [IndicateurController::class, 'validerValeur'])
        ->name('indicateurs.valider-valeur');

    // ── Documents / GED ───────────────────────────────────────────────────
    Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
    Route::post('documents/{document}/valider', [DocumentController::class, 'valider'])
        ->name('documents.valider');
    Route::post('documents/{document}/archiver', [DocumentController::class, 'archiver'])
        ->name('documents.archiver');

    // ── API interne (JSON) ────────────────────────────────────────────────
    Route::get('/api/directions/{direction}/services', function (Direction $direction) {
        return $direction->services()->actif()->orderBy('libelle')->get(['id', 'libelle']);
    })->name('api.direction.services');

    // ── Alertes ───────────────────────────────────────────────────────────
    Route::resource('alertes', AlerteController::class)->only(['index', 'show']);
    Route::post('alertes/{alerte}/traiter', [AlerteController::class, 'traiter'])
        ->name('alertes.traiter');
    Route::post('alertes/{alerte}/escalader', [AlerteController::class, 'escalader'])
        ->name('alertes.escalader');
    Route::post('papas/{papa}/alertes/generer', [AlerteController::class, 'generer'])
        ->name('papas.alertes.generer');

    // ── Rapports ──────────────────────────────────────────────────────────
    Route::resource('rapports', RapportController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('rapports/{rapport}/valider', [RapportController::class, 'valider'])->name('rapports.valider');
    Route::post('rapports/{rapport}/publier', [RapportController::class, 'publier'])->name('rapports.publier');
    Route::get('rapports/{rapport}/export-pdf', [RapportController::class, 'exportPdf'])->name('rapports.export-pdf');
    Route::get('papas/{papa}/export-excel',    [RapportController::class, 'exportExcel'])->name('rapports.export-excel');
    Route::get('papas/{papa}/export-pdf',      [RapportController::class, 'exportPapaPdf'])->name('rapports.export-papa-pdf');

    // ── Budget ────────────────────────────────────────────────────────────
    Route::prefix('papas/{papa}')->name('budgets.')->group(function () {
        Route::get('budget',              [BudgetController::class, 'index'])->name('index');
        Route::get('budget/create',       [BudgetController::class, 'create'])->name('create');
        Route::post('budget',             [BudgetController::class, 'store'])->name('store');
        Route::get('budget/{budget}/edit',[BudgetController::class, 'edit'])->name('edit');
        Route::put('budget/{budget}',     [BudgetController::class, 'update'])->name('update');
        Route::delete('budget/{budget}',  [BudgetController::class, 'destroy'])->name('destroy');
    });

    // ── Risques ───────────────────────────────────────────────────────────
    Route::prefix('papas/{papa}')->name('risques.')->group(function () {
        Route::get('risques',              [RisqueController::class, 'index'])->name('index');
        Route::get('risques/create',       [RisqueController::class, 'create'])->name('create');
        Route::post('risques',             [RisqueController::class, 'store'])->name('store');
        Route::get('risques/{risque}/edit',[RisqueController::class, 'edit'])->name('edit');
        Route::put('risques/{risque}',     [RisqueController::class, 'update'])->name('update');
        Route::delete('risques/{risque}',  [RisqueController::class, 'destroy'])->name('destroy');
    });

    // ── Administration ────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {
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
});
