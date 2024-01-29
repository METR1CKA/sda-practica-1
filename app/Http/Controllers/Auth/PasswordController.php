<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
  public function update(Request $request)
  {
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

    $request->user()->update([
      'password' => Hash::make($validated['password']),
    ]);

    return back()->with('status', 'password-updated');
  }
}
