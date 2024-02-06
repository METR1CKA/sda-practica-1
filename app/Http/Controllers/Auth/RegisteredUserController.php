<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPostRequest;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
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
    Log::info('SEND VIEW REGISTER', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to register',
      'CONTROLLER' => RegisteredUserController::class,
      'USER' => Auth::user() ?? 'GUEST',
      'METHOD' => 'create',
    ]);

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
    Log::info('REQUEST TO REGISTER', [
      'ACTION' => 'Register',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'CONTROLLER' => RegisteredUserController::class,
      'METHOD' => 'store',
    ]);

    $data = $request->validated();

    DB::beginTransaction();

    Log::info('VALIDATION TO REGISTER PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Register',
      'USER' => $request->user(),
    ]);

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
        'phone' => null,
      ]);

      if ($role_id == $roles['ADMIN']) {
        $user->twoFA()->create([
          'code2fa' => null,
          'code2fa_verified' => false,
        ]);
      }

      Log::info('USER CREATED', [
        'STATUS' => 'SUCCESS',
        'ACTION' => 'Register',
        'USER' => $user,
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();

      error_log($e->getMessage());

      Log::error('ERROR CREATING USER', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Register',
        'USER' => $request->user(),
        'MESSAGE' => $e->getMessage(),
        'LINE_CODE' => $e->getLine(),
        'TRACE' => $e->getTraceAsString(),
        'FILE' => $e->getFile(),
      ]);

      return redirect()
        ->back()
        ->withErrors([
          'password_confirmation' => 'There was an error creating the user.',
        ]);
    }

    return redirect(RouteServiceProvider::HOME);
  }
}
