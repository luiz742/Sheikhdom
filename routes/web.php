<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('/expensesa/analytics', [ExpenseController::class, 'analytics'])->name('expenses.analytics');

});

// Grupo de rotas administrativas
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('role:admin')->group(function () {

        //Users
        Route::post('/teams/{team}/add-user', [UserController::class, 'addUserToTeam'])->name('teams.addUser');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assignRole');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
