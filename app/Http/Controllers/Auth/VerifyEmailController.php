<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

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
    Log::info('REQUEST TO VERIFY EMAIL', [
      'ACTION' => 'Verify email',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => VerifyEmailController::class,
      'METHOD' => '__invoke',
    ]);

    if ($request->user()->hasVerifiedEmail()) {
      return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }

    if ($request->user()->markEmailAsVerified()) {
      event(new Verified($request->user()));
    }

    Log::info('EMAIL VERIFIED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Verify email',
    ]);

    return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
  }
}
