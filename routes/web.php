<?php

use App\Http\Controllers\ProfileController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
  $get_user_session = Auth::user();

  $user = User::find($get_user_session->id);

  $roles = Role::getRoles();

  return $user->role->id == $roles['ADMIN'] ? view('welcome') : redirect()->route('dashboard');
})
  ->name('/')
  ->middleware(['auth', 'verified']);

Route::get('/dashboard', function () {
  return view('dashboard');
})
  ->middleware(['auth', 'verified'])
  ->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

  Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

  Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
