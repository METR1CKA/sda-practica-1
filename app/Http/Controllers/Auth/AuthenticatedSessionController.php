<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
  /**
   * Muestra la vista de inicio de sesión.
   *
   * @return \Illuminate\View\View
   */
  public function create(): View
  {
    return view('auth.login');
  }

  /**
   * Maneja una solicitud de autenticación entrante.
   *
   * @param  \App\Http\Requests\Auth\LoginRequest  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    $request->authenticate();

    $request->session()->regenerate();

    Log::info('LOGIN', [
      'STATUS' => 'SUCCESS',
      'DATA' => [
        'INFO' => 'AuthenticatedSessionController::store()',
        'USER' => Auth::user(),
      ]
    ]);

    return redirect()->intended(RouteServiceProvider::HOME);
  }

  /**
   * Destruye una sesión autenticada.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Request $request): RedirectResponse
  {
    $current_user = Auth::user();

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    Log::info('LOGOUT', [
      'STATUS' => 'SUCCESS',
      'DATA' => [
        'INFO' => 'AuthenticatedSessionController::destroy()',
        'USER' => $current_user,
        'REVOKE' => true,
      ]
    ]);

    return redirect('/');
  }
}
