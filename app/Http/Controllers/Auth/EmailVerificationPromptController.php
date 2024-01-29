<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para mostrar el formulario de verificación de correo electrónico.
 */
class EmailVerificationPromptController extends Controller
{

  /**
   * Muestra el formulario de verificación de correo electrónico.
   *
   * @param  Request  $request
   * @return RedirectResponse|View
   */
  public function __invoke(Request $request): RedirectResponse|View
  {
    return $request->user()->hasVerifiedEmail()
      ? redirect()->intended(RouteServiceProvider::HOME)
      : view('auth.verify-email');
  }
}
