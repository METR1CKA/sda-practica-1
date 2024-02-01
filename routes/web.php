<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'verified', 'password.confirmed'])->group(function () {
  // Ruta principal
  Route::get('/', function () {
    Log::info('SEND VIEW DASHBOARD OR WELCOME', [
      'USER' => Auth::user(),
    ]);

    return view('welcome');
  })
    ->middleware(['admin.view.welcome'])
    ->name('/');

  // Ruta de dashboard
  Route::get('/dashboard', function () {
    Log::info('SEND VIEW DASHBOARD', [
      'USER' => Auth::user(),
    ]);

    return view('dashboard');
  })
    ->name('dashboard');

  // Rutas de usuarios
  Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');

  Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->name('users.destroy');

  // Rutas de perfil
  Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

  Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

  Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
