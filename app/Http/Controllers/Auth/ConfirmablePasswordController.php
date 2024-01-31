<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controlador para confirmar la contraseña del usuario.
 */
class ConfirmablePasswordController extends Controller
{
  /**
   * Muestra la vista de confirmación de contraseña.
   *
   * @return \Illuminate\View\View
   */
  public function show(): View
  {
    Log::info('SEND VIEW CONFIRM PASSWORD', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to confirm password',
      'USER' => Auth::user() ?? 'GUEST',
      'CONTROLLER' => ConfirmablePasswordController::class,
      'METHOD' => 'show',
    ]);

    return view('auth.confirm-password');
  }

  /**
   * Confirma la contraseña del usuario.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\RedirectResponse
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    Log::info('REQUEST TO CONFIRM PASSWORD', [
      'ACTION' => 'Confirm password',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => Auth::user(),
      'CONTROLLER' => ConfirmablePasswordController::class,
      'METHOD' => 'store',
    ]);

    $validate = Auth::guard('web')->validate([
      'email' => $request->user()->email,
      'password' => $request->password,
    ]);

    if (!$validate) {
      Log::alert('VALIDATION NOT PASSED', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Confirm password',
        'USER' => Auth::user(),
        'VALIDATE' => $validate,
      ]);

      throw ValidationException::withMessages([
        'password' => __('auth.password'),
      ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    Log::info('VALIDATION PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Confirm password',
      'USER' => Auth::user(),
      'VALIDATE' => $validate,
      'SESSION' => $request->session()->all(),
    ]);

    return redirect()->intended(RouteServiceProvider::HOME);
  }
}
