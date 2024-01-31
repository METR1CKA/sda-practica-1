<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para enviar notificaciones de verificación de correo electrónico.
 */
class EmailVerificationNotificationController extends Controller
{

  /**
   * Almacena y envía la notificación de verificación de correo electrónico.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    Log::info('REQUEST TO SEND EMAIL VERIFICATION NOTIFICATION', [
      'ACTION' => 'Send email verification',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => EmailVerificationNotificationController::class,
      'METHOD' => 'store',
    ]);

    if ($request->user()->hasVerifiedEmail()) {
      Log::alert('USER ALREADY VERIFIED', [
        'STATUS' => 'SUCCESS',
        'ACTION' => 'Send email verification',
        'USER' => $request->user(),
      ]);

      return redirect()->intended(RouteServiceProvider::HOME);
    }

    $request->user()->sendEmailVerificationNotification();

    Log::info('EMAIL VERIFICATION NOTIFICATION SENT', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Send email verification',
      'USER' => $request->user(),
    ]);

    return back()->with('status', 'verification-link-sent');
  }
}
