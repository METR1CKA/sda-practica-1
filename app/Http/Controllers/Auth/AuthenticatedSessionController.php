<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
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
    Log::info('SEND VIEW LOGIN', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to login',
      'USER' => Auth::user() ?? 'GUEST',
      'CONTROLLER' => AuthenticatedSessionController::class,
      'METHOD' => 'create',
    ]);

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
    Log::info('REQUEST TO LOGIN', [
      'ACTION' => 'Authenticate',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'SESSION' => $request->session()->all(),
      'USER_AGENT' => $request->userAgent(),
      'CONTROLLER' => AuthenticatedSessionController::class,
      'METHOD' => 'store',
    ]);

    $request->authenticate();

    $request->session()->regenerate();

    Log::info('LOGIN', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Authenticate',
      'SESSION' => $request->session()->all(),
      'USER' => Auth::user(),
      'REDIRECT_TO' => RouteServiceProvider::HOME,
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
    Log::info('REQUEST TO LOGOUT', [
      'ACTION' => 'Revoke session',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => Auth::user(),
      'CONTROLLER' => AuthenticatedSessionController::class,
      'METHOD' => 'destroy',
    ]);

    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    Log::info('LOGOUT', [
      'STATUS' => 'SUCCESS',
      'SESSION' => $request->session()->all(),
      'REVOKE_SESSION' => Auth::check() ? 'NO' : 'YES',
    ]);

    return redirect('/');
  }
}
