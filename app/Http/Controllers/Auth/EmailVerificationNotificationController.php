<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    if ($request->user()->hasVerifiedEmail()) {
      return redirect()->intended(RouteServiceProvider::HOME);
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
  }
}
