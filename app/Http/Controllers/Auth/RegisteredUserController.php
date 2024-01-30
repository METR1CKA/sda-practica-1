<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPostRequest;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para registrar usuarios.
 */
class RegisteredUserController extends Controller
{
  /**
   * Muestra la vista de registro.
   *
   * @return \Illuminate\View\View
   */
  public function create(): View
  {
    return view('auth.register');
  }

  /**
   * Crea un nuevo usuario.
   *
   * @param  \App\Http\Requests\RegisterPostRequest  $request
   * @return \Illuminate\Http\RedirectResponse
   * 
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(RegisterPostRequest $request): RedirectResponse
  {
    $data = $request->validated();

    Log::info('REGISTER', [
      'STATUS' => 'SUCCESS',
      'DATA' => [
        'INFO' => 'RegisteredUserController::store()',
        'DATA' => $data,
      ]
    ]);

    DB::beginTransaction();

    try {
      $users = User::all();

      $roles = Role::getRoles();

      $role_id = $users->count() == 0 ? $roles['ADMIN'] : $roles['GENERIC'];

      $user = User::create([
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role_id' => $role_id,
        'active' => true,
      ]);

      Log::info('RegisteredUserController::store()::57', ['USER' => $user->username, 'ROLE' => $user->role->name]);

      Log::info('USER CREATE', [
        'STATUS' => 'SUCCESS',
        'DATA' => [
          'INFO' => 'RegisteredUserController::store()',
          'USER' => $user,
        ]
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();

      Log::error('RegisteredUserController::store()::63', ['ERROR' => $e->getMessage(), 'LINE_CODE', '71']);

      Log::error('USER CREATE', [
        'STATUS' => 'ERROR',
        'MESSAGE' => $e->getMessage(),
        'DATA' => [
          'INFO' => 'RegisteredUserController::store()',
          'LINE_CODE' => $e->getLine(),
        ]
      ]);

      return redirect()
        ->back()
        ->withErrors([
          'password_confirmation' => 'There was an error creating the user.',
        ]);
    }

    event(new Registered($user));

    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
  }
}
