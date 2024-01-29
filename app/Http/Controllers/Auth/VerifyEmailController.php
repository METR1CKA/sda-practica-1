<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador para verificar direcciones de correo electrónico.
 */
class VerifyEmailController extends Controller
{
  /**
   * Marca el correo electrónico del usuario como verificado.
   *
   * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function __invoke(EmailVerificationRequest $request): RedirectResponse
  {
    if ($request->user()->hasVerifiedEmail()) {
      return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }

    if ($request->user()->markEmailAsVerified()) {
      event(new Verified($request->user()));
    }

    return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
  }
}
