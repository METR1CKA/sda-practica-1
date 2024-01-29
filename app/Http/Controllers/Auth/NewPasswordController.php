<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Controlador para restablecer contraseñas.
 */
class NewPasswordController extends Controller
{
  /**
   * Muestra la vista de restablecimiento de contraseña.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function create(Request $request): View
  {
    return view('auth.reset-password', ['request' => $request]);
  }

  /**
   * Restablece la contraseña del usuario.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'token' => ['required'],
      'email' => ['required', 'email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Aquí intentaremos restablecer la contraseña del usuario. Si tiene éxito,
    // actualizaremos la contraseña en un modelo de usuario real y lo persistiremos
    // en la base de datos. De lo contrario, analizaremos el error y devolveremos la respuesta.
    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user) use ($request) {
        $user->forceFill([
          'password' => Hash::make($request->password),
          'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
      }
    );

    // Si la contraseña se restableció correctamente, redirigiremos al usuario a la vista autenticada de inicio de la aplicación.
    // Si hay un error, podemos redirigirlos de vuelta a donde vinieron con su mensaje de error.
    return $status == Password::PASSWORD_RESET
      ? redirect()->route('login')->with('status', __($status))
      : back()->withInput($request->only('email'))
      ->withErrors(['email' => __($status)]);
  }
}
