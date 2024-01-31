<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Controlador para restablecer contraseñas.
 */
class PasswordResetLinkController extends Controller
{
  /**
   * Muestra la vista de restablecimiento de contraseña.
   *
   * @return \Illuminate\View\View
   */
  public function create(): View
  {
    Log::info('SEND VIEW FORGOT PASSWORD', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Show view to forgot password',
      'CONTROLLER' => PasswordResetLinkController::class,
      'USER' => Auth::user() ?? 'GUEST',
      'METHOD' => 'create',
    ]);

    return view('auth.forgot-password');
  }

  /**
   * Envía un enlace de restablecimiento de contraseña al usuario.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   * 
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    Log::info('REQUEST TO SEND RESET PASSWORD LINK', [
      'ACTION' => 'Send reset password link',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'CONTROLLER' => PasswordResetLinkController::class,
      'METHOD' => 'store',
    ]);

    $request->validate([
      'email' => ['required', 'email'],
      'g-recaptcha-response' => ['required', new Recaptcha],
    ]);

    Log::info('VALIDATION TO SEND RESET PASSWORD LINK PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Send reset password link',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user->active) {
      Log::alert('USER NOT ACTIVE', [
        'STATUS' => 'ERROR',
        'ACTION' => 'Send reset password link',
        'USER' => $user,
      ]);

      return back()
        ->withErrors([
          'email' => 'No user was found with that email.',
        ]);
    }

    // Enviaremos el enlace de restablecimiento de contraseña a este usuario. Una vez que hayamos intentado
    // enviar el enlace, examinaremos la respuesta y veremos el mensaje que debemos mostrar al usuario.
    // Finalmente, enviaremos una respuesta adecuada.
    $status = Password::sendResetLink(
      $request->only('email')
    );

    $data = [
      'STATUS' => $status == Password::PASSWORD_RESET ? 'SUCCESS' : 'FAILED',
      'ACTION' => 'Send reset password link',
      'USER' => $request->user(),
      'PASSWORD-STATUS' => $status,
    ];

    $msg = "PASSWORD RESET" . $status == Password::PASSWORD_RESET
      ? 'COMPLETED'
      : 'FAILED';

    $status == Password::PASSWORD_RESET
      ? Log::info($msg, $data)
      : Log::alert($msg, $data);

    return $status == Password::RESET_LINK_SENT
      ? back()->with('status', __($status))
      : back()->withInput($request->only('email'))
      ->withErrors(['email' => __($status)]);
  }
}
