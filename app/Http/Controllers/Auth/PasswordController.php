<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

/**
 * Controlador para actualizar la contraseña del usuario.
 */
class PasswordController extends Controller
{
  /**
   * Muestra la vista de actualización de contraseña.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request): RedirectResponse
  {
    Log::info('REQUEST TO UPDATE PASSWORD', [
      'ACTION' => 'Update password',
      'HTTP-VERB' => $request->method(),
      'URL' => $request->url(),
      'IP' => $request->ip(),
      'USER_AGENT' => $request->userAgent(),
      'SESSION' => $request->session()->all(),
      'USER' => $request->user(),
      'CONTROLLER' => PasswordController::class,
      'METHOD' => 'update',
    ]);

    $validated = $request->validateWithBag('updatePassword', [
      'current_password' => ['required', 'current_password'],
      'password' => [
        'required',
        Password::min(8)
          ->max(12)
          ->mixedCase()
          ->letters()
          ->numbers()
          ->symbols()
          ->uncompromised(5),
        'confirmed'
      ],
      'g-recaptcha-response' => ['required', new Recaptcha],
    ]);

    Log::info('VALIDATION TO UPDATE PASSWORD PASSED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Update password',
      'USER' => $request->user(),
    ]);

    $request->user()->update([
      'password' => Hash::make($validated['password']),
    ]);

    Log::info('PASSWORD UPDATED', [
      'STATUS' => 'SUCCESS',
      'ACTION' => 'Update password',
      'USER' => $request->user(),
    ]);

    return back()->with('status', 'password-updated');
  }
}
