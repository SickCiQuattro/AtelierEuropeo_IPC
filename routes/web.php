<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePasswordController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;

// Includi le route di autenticazione
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Route Linguaggio
|--------------------------------------------------------------------------
*/

// Route per il cambio lingua
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['it', 'en', 'es', 'fr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');


/*
|--------------------------------------------------------------------------
| Route Pubbliche (Ospiti e Autenticati)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [FrontController::class, 'getHome'])->name('home');

// Route compatibile con gli scaffold auth Laravel/Breeze usati dai test
Route::get('/dashboard', function () {
    $user = request()->user();

    return $user?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

// Pagine statiche
Route::get('/about', [FrontController::class, 'getAbout'])->name('about');
Route::get('/contact', [FrontController::class, 'getContact'])->name('contact');

// Pagine viaggiare all'estero
Route::get('/corpo-europeo-solidarieta', [FrontController::class, 'getCorpoEuropeo'])->name('corpo-europeo');
Route::get('/scambi-giovanili', [FrontController::class, 'getScambiGiovanili'])->name('scambi-giovanili');
Route::get('/corsi-formazione', [FrontController::class, 'getCorsiFormazione'])->name('corsi-formazione');

// Portfolio (progetti completati con testimonianze)
Route::get('/portfolio', [ProjectController::class, 'portfolio'])->name('project.portfolio');

// Lista progetti (con middleware di controllo accessi)

Route::get('/project', [ProjectController::class, 'index'])
    ->middleware('checkProjectAccess')
    ->name('project.index');

// Route creazione progetto solo admin (deve essere prima della route wildcard {project})
Route::get('/project/create', [ProjectController::class, 'create'])
    ->middleware(['auth', 'isAdmin'])
    ->name('project.create');

// Vista singolo progetto (pubblica)
Route::get('/project/{project}', [ProjectController::class, 'show'])->name('project.show');

// Toggle preferiti progetto (AJAX)
Route::post('/projects/{project}/favorite', [FavoriteController::class, 'toggle'])
    ->name('project.favorite.toggle')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Route Utenti Autenticati
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Route Profilo Utente
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        
        // Route aggiornamento password
        Route::patch('/password', [ProfilePasswordController::class, 'update'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Route Candidature Utente
    |--------------------------------------------------------------------------
    */
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [ApplicationController::class, 'show'])->name('show');
    });

    // Route candidatura progetti (nidificate sotto project)
    Route::prefix('project/{id}')->name('applications.')->group(function () {
        Route::get('/apply', [ApplicationController::class, 'create'])->name('create');
        Route::post('/apply', [ApplicationController::class, 'store'])->name('store');
    });

    /*
    |--------------------------------------------------------------------------
    | Route Preferiti
    |--------------------------------------------------------------------------
    */
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/toggle', [FavoriteController::class, 'toggleLegacy'])->name('toggle');
    });
});

/*
|--------------------------------------------------------------------------
| Route Solo Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'isAdmin'])->group(function () {

    // Toggle vista admin/vista utente per account amministratore
    Route::get('/admin/view-user', function () {
        session(['admin_user_view' => true]);
        return redirect()->route('home');
    })->name('admin.view-user');

    Route::get('/admin/return', function () {
        session()->forget('admin_user_view');
        return redirect()->route('admin.dashboard');
    })->name('admin.return');
    
    /*
    |--------------------------------------------------------------------------
    | Route Dashboard Admin
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [ProjectController::class, 'dashboard'])->name('admin.dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Route Gestione Progetti (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('project')->name('project.')->group(function () {
        // Operazioni CRUD progetti (route create spostata sopra)
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('destroy');
        
        // Gestione stato progetti
        Route::get('/{id}/destroy/confirm', [ProjectController::class, 'confirmDestroy'])->name('destroy.confirm');
        Route::get('/{id}/complete/confirm', [ProjectController::class, 'confirmCompletion'])->name('confirm.completion');
        Route::put('/{id}/complete', [ProjectController::class, 'complete'])->name('complete');
        
        // Route validazione AJAX
        Route::post('/validate', [ProjectController::class, 'validateAjax'])->name('validate');
        Route::post('/check-title', [ProjectController::class, 'checkTitleUnique'])->name('checkTitle');
    });

    /*
    |--------------------------------------------------------------------------
    | Route Gestione Candidature (Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Gestione candidature progetti
        Route::prefix('project/{projectId}')->group(function () {
            Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
        });
        
        // Gestione candidature individuali
        Route::prefix('applications/{application}')->name('applications.')->group(function () {
            Route::get('/', [AdminApplicationController::class, 'show'])->name('show');
            Route::patch('/update-status', [AdminApplicationController::class, 'updateStatus'])->name('update-status');
            Route::patch('/approve', [AdminApplicationController::class, 'approve'])->name('approve');
            Route::patch('/reject', [AdminApplicationController::class, 'reject'])->name('reject');
        });
    });
});