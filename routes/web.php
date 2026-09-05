<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\CollaborateurDashboardController;
use App\Http\Controllers\CollaborateurPlanningController;
use App\Http\Controllers\DemandeReservationController;
use App\Http\Controllers\ProfileController;

Auth::routes();

/*
|--------------------------------------------------------------------------
| Redirection de la racine
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Redirection après connexion
|--------------------------------------------------------------------------
*/

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

/*
|--------------------------------------------------------------------------
| Routes authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/email/verification-notification', function () {
        return redirect()->route('profile.edit');
    })->name('verification.send');

    /*
    |--------------------------------------------------------------------------
    | Gestionnaires et administrateurs
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:GESTIONNAIRE,ADMINISTRATEUR')
        ->group(function () {

            /*
            |------------------------------------------------------------------
            | Planning général
            |------------------------------------------------------------------
            */

            Route::prefix('planning')
                ->name('planning.')
                ->controller(PlanningController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/reservations/{reservation}/edit', 'edit')->name('reservations.edit');
                    Route::put('/reservations/{reservation}', 'update')->name('reservations.update');
                });

            /*
            |------------------------------------------------------------------
            | Voitures
            |------------------------------------------------------------------
            */

            Route::prefix('voitures')
                ->name('voitures.')
                ->controller(VoitureController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                });
        });

    /*
    |--------------------------------------------------------------------------
    | Administrateur uniquement
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:ADMINISTRATEUR')
        ->group(function () {

            Route::prefix('utilisateurs')
                ->name('utilisateurs.')
                ->controller(UserController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{user}', 'update')->name('update');
                    Route::delete('/{user}', 'destroy')->name('destroy');
                });
        });

    /*
    |--------------------------------------------------------------------------
    | Collaborateurs uniquement
    |--------------------------------------------------------------------------
    */

        Route::middleware(['auth', 'role:COLLABORATEUR'])
            ->prefix('collaborateur')
            ->name('collaborateur.')
            ->group(function () {

            Route::get(
                '/dashboard',
                [CollaborateurDashboardController::class, 'index']
            )->name('dashboard');

            Route::post(
                '/demandes',
                [DemandeReservationController::class, 'store']
            )->name('demandes.store');

            Route::get(
                '/demandes/{reservation}/edit',
                [CollaborateurDashboardController::class, 'edit']
            )->name('demandes.edit');

            Route::put(
                '/demandes/{reservation}',
                [CollaborateurDashboardController::class, 'update']
            )->name('demandes.update');

            Route::delete(
                '/demandes/{reservation}',
                [CollaborateurDashboardController::class, 'destroy']
            )->name('demandes.destroy');
        });
});