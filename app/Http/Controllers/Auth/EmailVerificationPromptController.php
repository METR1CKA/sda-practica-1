<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    Log::info('SEND VIEW OR REDIRECT IF USER HAS VERIFIED EMAIL', [
      'ACTION' => 'Send view or redirect if user has verified email',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => EmailVerificationPromptController::class,
      'METHOD' => '__invoke',
    ]);

    return $request->user()->hasVerifiedEmail()
      ? redirect()->intended(RouteServiceProvider::HOME)
      : view('auth.verify-email');
  }
}
