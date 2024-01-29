<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controlador para confirmar la contraseña del usuario.
 */
class ConfirmablePasswordController extends Controller
{
  /**
   * Muestra la vista de confirmación de contraseña.
   *
   * @return \Illuminate\View\View
   */
  public function show(): View
  {
    return view('auth.confirm-password');
  }

  /**
   * Confirma la contraseña del usuario.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\RedirectResponse
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $validate = Auth::guard('web')->validate([
      'email' => $request->user()->email,
      'password' => $request->password,
    ]);

    if (!$validate) {
      throw ValidationException::withMessages([
        'password' => __('auth.password'),
      ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());

    return redirect()->intended(RouteServiceProvider::HOME);
  }
}
